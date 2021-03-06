<?php

date_default_timezone_set('Europe/London');

set_include_path ('../libs');

include_once 'utils.php';
include_once 'db.php';
include_once 'xml.php';

include_once 'climber.php';
include_once 'colour.php';
include_once 'date.php';
include_once 'difficulty.php';
include_once 'success.php';

include 'db_names.php';

function climb_get_panels ($db, $panels)
{
	global $DB_V_ROUTE;

	$table   = $DB_V_ROUTE;
	$columns = array ('id', 'panel', 'colour', 'grade', 'climb_type');
	$where   = "panel in ('" . implode ("','", $panels) . "')";
	$order   = "grade_seq, colour";

	return db_select ($db, $table, $columns, $where, $order);
}

function climb_lookup_success ($db, $text)
{
	$success = success_get($db);

	foreach ($success as $s) {
		if ($s['outcome'] == $text)
			return $s['id'];
	}

	return null;
}

function climb_lookup_difficulty ($db, $text)
{
	$difficulty = difficulty_get($db);

	foreach ($difficulty as $s) {
		if ($s['description'] == $text)
			return $s['id'];
	}

	return null;
}

function climb_get_route_id ($xml, $panels, $name, $colour)
{
	foreach ($panels as $p) {
		if (($p['panel'] == $name) &&
		    ($p['colour'] == $colour)) {
			return $p['id'];
		}
	}

	return null;
}

function climb_find_rating_ids ($db, $rids)
{
	global $DB_RATING;

	$table = $DB_RATING;
	$columns = array ("route_id as id", "id as rating_id");

	$where = "route_id in ('" . implode ("','", $rids) . "')";

	return db_select ($db, $table, $columns, $where);
}

function climb_lookup_climber (&$xml, $name)
{
	$climbers = climber_get($db);

	foreach ($climbers as $c) {
		if (strcasecmp ($c['name'], $name) == 0) {
			return $c['id'];
		}
	}

	xml_add_error ($xml, sprintf ("'%s' is not a valid climber", $name));
	return null;
}


function climb_parse_climb ($db, &$xml, $text)
{
	//      clean
	// (c)  clean
	// (s)  success
	// (f)  failed
	// (d)  downclimb
	// (1r) 1 rest
	// (1f) 1 fall
	// (n)  nice
	// (mX) many X

	$result = array ('colour' => '', 'success' => '', 'notes' => '', 'nice' => false);

	$text = trim ($text);
	$parts = preg_split('/[\s,()]+/', $text);
	foreach ($parts as $key => $p) {
		if (empty ($p)) {
			unset ($parts[$key]);
		}
	}

	$colour = strtolower ($parts[0]);
	if (array_key_exists (1, $parts))
		$note = strtolower ($parts[1]);
	else
		$note = "";

	if ($colour == 'all') {
		// We'll deal with this later
		$result['colour'] = $colour;
	} else {
		$c = colour_match ($db, $colour);
		if ($c === null) {
			xml_add_error ($xml, sprintf ("'%s' is not a valid colour\n", $colour));
			return null;
		}
		$result['colour'] = $c['colour'];
	}

	// 1: strip out nice [n]
	$pos = strpos ($note, 'n');
	if ($pos !== false) {
		$result['nice'] = true;
		$note = str_replace ('n', '', $note);
	}

	$count = 0;
	if (strlen ($note) > 1) {
		// 2: strip out count [1-9]
		$n = $note[0];
		if (($n > 0) && ($n <= 9)) {
			$count = $n;
			$note = substr ($note, 1);
		}

		// 3: strip out count [m]
		if ($n == 'm') {
			$count = 'many';
			$note = substr ($note, 1);
		}

		if ($count === 0) {
			xml_add_error ($xml, sprintf ("Bad multiple: %s\n", $n));
			return null;
		}

		if ($note == 'f') {
			$type = "fall";
		} else if ($note == 'r') {
			$type = "rest";
		} else {
			xml_add_error ($xml, sprintf ("Unknown type: %s\n", $note));
			return null;
		}

		if ($count != 1)
			$plural = "s";
		else
			$plural = "";
		$result['notes'] = sprintf ("%s %s%s", $count, $type, $plural);
		$note = 's';
	}

	// 4: parse success [fscd ]
	switch ($note) {
		case 'f':
			$success = 'failed';
			break;
		case 's':
			$success = 'success';
			break;
		case 'd':
			$success = 'downclimb';
			break;
		case 'c':
		case ' ':
		case '':
		default:
			$success = 'clean';
			break;
	}

	$result['success'] = $success;

	return $result;
}


function climb_commit_climb ($db, &$xml, $climbs)
{
	$query  = "insert into climb (climber_id, route_id, success_id, date_climbed) values ";
	$values = array();

	foreach ($climbs as $c) {
		$values[] = '(' .
				$c['climber_id']   . ',' .
				$c['route_id']     . ',' .
				$c['success_id']   . ',' .
			"'" .	$c['date_climbed'] . "'" .
			')';
	}

	$query .= implode (',', $values);

	$result = $db->query($query);
	if ($result === true) {
		xml_add_error ($xml, sprintf ("Created %d climbs", $db->affected_rows()));
		//printf ("Created %d climbs\n\n", mysql_affected_rows());
		return $db->affected_rows();
	} else {
		return -1;
	}
}

function climb_commit_rating ($db, &$xml, $ratings)
{
	// split the ratings into "insert" or "update"

	$rids = array();
	foreach ($ratings as $r) {
		$route_id = $r['route_id'];
		$rids[$route_id] = $route_id;
	}

	$rating_ids = climb_find_rating_ids ($db, $rids);

	$count_insert = 0;
	$count_update = 0;
	$values_insert = array();
	$values_update = array();
	foreach ($ratings as $r) {
		if (($r['nice'] == 'nice') || ($r['nice'] == '1'))
			$r['nice'] = 1;
		else
			$r['nice'] = 0;

		if (empty ($r['difficulty_id'])) {
			$r['difficulty_id'] = 'null';
		}

		if (empty ($r['notes'])) {
			$r['notes'] = 'null';
		} else {
			$r['notes'] = "'" . str_replace ("'", "''", $r['notes']) . "'";
		}

		$route_id = $r['route_id'];
		if (array_key_exists ($route_id, $rating_ids)) {
			$r['rating_id'] = $rating_ids[$route_id]['rating_id'];
			$values_update[] = $r;
		} else {
			$values_insert[] = $r;
		}
	}

	//xml_add_error ($xml, print_r ($values_insert, true));
	if (count ($values_insert) > 0) {
		$query  = "insert into rating (climber_id, route_id, difficulty_id, notes, nice) values ";
		$values = array();
		foreach ($values_insert as $r) {
			$values[] = '(' .
					$r['climber_id']    . ',' .
					$r['route_id']      . ',' .
					$r['difficulty_id'] . ',' .
					$r['notes']         . ',' .
					$r['nice']          .
				')';
		}

		$query .= implode (',', $values);

		$result = $db->query($query);
		if ($result === true) {
			xml_add_error ($xml, sprintf ("Created %s new ratings", $db->affected_rows()));
		} else {
			xml_add_error ($xml, "Rating insert failed");
		}
	}

	if (count ($values_update) > 0) {
		foreach ($values_update as $r) {
			$query = "update rating set ";
			$query .= "climber_id    = " . $r['climber_id']    . ',';
			$query .= "route_id      = " . $r['route_id']      . ',';
			$query .= "difficulty_id = " . $r['difficulty_id'] . ',';
			$query .= "nice          = " . $r['nice']          . ',';
			$query .= "notes         = " . $r['notes']         . ' ';

			$query .= "where id = " . $r['rating_id'];

			$result = $db->query($query);
			if ($result === true) {
				$count_update++;
			} else {
				xml_add_error ($xml, "Rating update failed");
			}
		}

		if ($count_update > 0) {
			xml_add_error ($xml, sprintf ("Updated %s existing ratings", $count_update));
		}
	}
}


function climb_get_rating_notes ($db, &$xml, &$climbs)		// XXX $xml isn't used
{
	global $DB_RATING;
	global $DB_SUCCESS;
	global $DB_DIFFICULTY;

	// for each climb, lookup the rating and notes
	$ids = array();
	foreach ($climbs as $r) {
		$ids[] = $r['id'];
	}

	$table =  "$DB_RATING left join $DB_DIFFICULTY on (difficulty_id = $DB_DIFFICULTY.id)";

	$columns = array ("route_id as id",
			  "$DB_DIFFICULTY.description as difficulty",
			  "nice",
			  "notes");

	$where = "route_id in (" . implode (',', $ids) . ')';

	$ratings = db_select ($db, $table, $columns, $where);

	foreach ($climbs as &$c) {
		$rid = $c['id'];
		if (array_key_exists ($rid, $ratings)) {
			$n = $c['notes'];
			$c['notes'] = $ratings[$rid]['notes'];
			if (!empty ($n)) {
				$c['notes'] .= "; " . $n;
			}
			$c['nice']       |= $ratings[$rid]['nice'];
			$c['difficulty']  = $ratings[$rid]['difficulty'];
		} else {
			$c['difficulty'] = "";
		}
	}
}


function climb_do_add ($db, &$xml)
{
	if (!array_key_exists ('date', $_GET)) {
		xml_add_error ($xml, "No date");
		return;
	}

	$message = null;
	$date = date_match ($_GET['date'], $message);
	if (empty ($date)) {
		xml_add_error ($xml, $message);
		return;
	}

	if (!array_key_exists ('climbs', $_GET)) {
		xml_add_error ($xml, "No climbs");
		return;
	} else {
		$climbs = trim ($_GET['climbs']);
		if (empty ($climbs)) {
			xml_add_error ($xml, "Empty climbs");
			return;
		}
	}

	$climbs  = $_GET['climbs'];

	$parts = preg_split('/[\s,]+/', $climbs);
	$panel_name = array_shift ($parts);
	foreach ($parts as $key => $p) {
		if ($p == '') {
			unset ($parts[$key]);
		}
	}

	$panel = climb_get_panels ($db, array ($panel_name));
	if (count ($panel) == 0) {
		xml_add_error ($xml, sprintf ("'%s' is not a valid panel name", $name));
		return;
	}

	$climbs = array();
	$errors = 0;
	foreach ($parts as $colour) {
		$p = climb_parse_climb ($db, $xml, $colour);
		if ($p === null) {
			$errors++;
			continue;
		}

		if ($p['colour'] == 'all') {
			foreach ($panel as $a) {
				$p['colour'] = $a['colour'];
				$climbs[] = array_merge ($a, $p);
			}
			continue;
		}

		// check that the colours actually exist
		foreach ($panel as $a) {
			if (strcasecmp ($p['colour'], $a['colour']) == 0) {
				$climbs[] = array_merge ($a, $p);
				continue 2;
			}
		}

		xml_add_error ($xml, sprintf ("Panel '%s' doesn't have a '%s' route", $panel, $p['colour']));
		$errors++;
	}

	if ($errors > 0)
		return;

	climb_get_rating_notes ($db, $xml, $climbs);

	foreach ($climbs as $c) {
		$climb = $xml->addChild ('route');
		$climb->climb_type = $c['climb_type'];
		$climb->colour     = $c['colour'];
		$climb->date       = $date;
		$climb->difficulty = $c['difficulty'];
		$climb->grade      = $c['grade'];
		$climb->id         = $c['id'];
		$climb->nice       = ($c['nice'] == 1) ? 'nice' : '';
		$climb->notes      = $c['notes'];
		$climb->panel      = $c['panel'];
		$climb->success    = $c['success'];
	}
}

function climb_do_save ($db, &$xml)
{
	global $_GET;

	if (!array_key_exists ('climb_xml', $_GET)) {
		xml_add_error ($xml, "No climb_xml");
		return;
	} else {
		$climbs = trim ($_GET['climb_xml']);
		if (empty ($climbs)) {
			xml_add_error ($xml, "Empty climbs");
			return;
		}
	}

	if (!array_key_exists ('climber', $_GET)) {
		xml_add_error ($xml, "No climber");
		return;
	}

	if (!climber_match_xml ($db, $xml, $_GET['climber']))
		return;

	$cxml = simplexml_load_string ($climbs);

	//printf ("committing %d climbs for %s\n", $cxml->count(), $_GET['climber']);

	// Collect the panel names
	$pnames = array();
	for ($i = 0; $i < $cxml->count(); $i++) {
		$p = (string) $cxml->climb[$i]->panel;
		$pnames[$p] = $p;
	}

	$panels = climb_get_panels ($db, $pnames);

	$commit_climb = array();
	$commit_rating = array();
	//printf ("\n");
	//printf ("rid   Panel Colour         Date         Success     Diff       Nice   Notes\n");
	for ($i = 0; $i < $cxml->count(); $i++) {
		$a = $cxml->climb[$i];
		$route_id = climb_get_route_id ($xml, $panels, urldecode ($a->panel), urldecode ($a->colour));
		if ($route_id === null) {
			xml_add_error ($xml, sprintf ("Route %s %s doesn't exist", urldecode ($a->panel), urldecode ($a->colour)));
			continue;
		}
		$message = null;
		$date_climbed = date_match (urldecode ($a->date), $message);
		if (empty ($date_climbed)) {
			xml_add_error ($xml, $message);
			continue;
		}

		$success_id = climb_lookup_success ($db, urldecode ($a->success));
		$difficulty_id = climb_lookup_difficulty ($db, urldecode ($a->difficulty));
		$nice = (string) urldecode ($a->nice);
		$notes = (string) urldecode ($a->notes);

		// Validate add_climb
		// Add climb using: climber_id, route_id, success_id, date_climbed
		$c = array();
		$c['climber_id']   = $xml->climber_id;
		$c['route_id']     = $route_id;
		$c['success_id']   = $success_id;
		$c['date_climbed'] = $date_climbed;
		$commit_climb[] = $c;

		// Validate add rating
		// Create new rating using: climber_id, route_id, difficulty_id, climb_note_id, nice
		$r = array();
		$r['climber_id']    = $xml->climber_id;
		$r['route_id']      = $route_id;
		$r['difficulty_id'] = $difficulty_id;
		$r['nice']          = $nice;
		$r['notes']         = htmlentities ($notes);
		$commit_rating[] = $r;
	}
	//printf ("\n");

	climb_commit_climb ($db, $xml, $commit_climb);
	climb_commit_rating ($db, $xml, $commit_rating);

	// for each <climb>
	//	lookup <panel> <colour>		[route_id]
	//	validate <date>			[date_climbed]
	//	validate <success>		[success_id]
	//	validate <difficulty>		[difficulty_id]
	//	validate <nice>			[nice]
	//	parse the <notes>		[notes]

	// Add climb using: climber_id, route_id, success_id, date_climbed

	// Does rating for <route_id> exist?
	// Yes:
	//	Does rating have a <climb_note_id>?
	//	Yes:
	//		Is the climb_note unique to this rating?
	//		Yes:
	//			Update note using: notes
	//		No:
	//			COW, create a new note using: notes
	//	No:
	//		Create a new note using: notes
	//	Update rating using: climber_id, route_id, difficulty_id, climb_note_id, nice
	// No:
	//	Does <notes> already exist in climb_note?
	//	Yes:
	//		Use existing note
	//	No:
	//		Create new note using: notes
	//	Create new rating using: climber_id, route_id, difficulty_id, climb_note_id, nice
}


function climb_main (&$xml)
{
	global $_GET;

	if (!array_key_exists ('action', $_GET)) {
		xml_add_error ($xml, "No action");
		return;
	}

	$action  = $_GET['action'];

	$db = db_get_database();

	switch ($action) {
	case 'add':
		climb_do_add ($db, $xml);
		break;
	case 'save':
		climb_do_save ($db, $xml);
		break;
	default:
		xml_add_error ($xml, sprintf ("'%s' is not a valid action", $action));
		break;
	}
}


header('Pragma: no-cache');
header('Content-Type: application/xml; charset=ISO-8859-1');
$xml = new SimpleXMLElement ("<?xml-stylesheet type='text/xsl' href='route.xsl'?"."><list />");
$xml->addAttribute ('type', 'climb');

climb_main ($xml);

echo $xml->asXML();

