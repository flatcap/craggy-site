<?php

set_include_path ('../../libs');

include 'colour.php';
include 'db.php';
include 'db_names.php';

date_default_timezone_set('UTC');

global $g_climbers;
global $g_success;
global $g_difficulty;

function climb_add_error (&$xml, $message)
{
	//printf ("ERROR: %s\n", $message);
	$xml->addChild ('error', $message);
}


function climb_get_panels ($panels)
{
	global $DB_V_ROUTE;

	$table   = $DB_V_ROUTE;
	$columns = array ('id', 'panel', 'colour', 'grade', 'climb_type');
	$where   = "panel in ('" . implode ("','", $panels) . "')";
	$order   = "grade_seq, colour";

	return db_select ($table, $columns, $where, $order);
}

function climb_get_all_panel ($name)
{
	global $g_routes;

	$results = array();
	foreach ($g_routes as $p) {
		if ($p['panel'] == $name) {
			$results[] = $p;
		}
	}

	return $results;
}

function climb_get_route_lookup()
{
	global $g_routes;

	$routes = array();
	foreach ($g_routes as $p) {
		$key = $p['panel'] . '_' . $p['colour'];
		$routes[$key] = $p['id'];
	}

	return $routes;
}

function climb_get_success()
{
	global $g_success;

	if (!$g_success) {
		global $DB_SUCCESS;

		$table   = $DB_SUCCESS;
		$columns = array ('id', 'outcome');

		$g_success = db_select ($table, $columns);
	}

	return $g_success;
}

function climb_lookup_success ($text)
{
	global $g_success;

	foreach ($g_success as $s) {
		if ($s['outcome'] == $text)
			return $s['id'];
	}

	return null;
}

function climb_get_difficulty()
{
	global $g_difficulty;

	if (!$g_difficulty) {
		global $DB_DIFFICULTY;

		$table   = $DB_DIFFICULTY;
		$columns = array ('id', 'description');

		$g_difficulty = db_select ($table, $columns);
	}

	return $g_difficulty;
}

function climb_lookup_difficulty ($text)
{
	global $g_difficulty;

	foreach ($g_difficulty as $s) {
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

function climb_find_rating_ids ($rids)
{
	global $DB_RATING;

	$table = $DB_RATING;
	$columns = array ("route_id as id", "id as rating_id");

	$where = "route_id in ('" . implode ("','", $rids) . "')";

	return db_select ($table, $columns, $where);
}

function climb_valid_command (&$xml)
{
	global $_GET;

	$success = true;

	if (!isset ($_GET)) {
		climb_add_error ($xml, "No get");
		$success = false;
	} else {
		if (!array_key_exists ('action', $_GET)) {
			climb_add_error ($xml, "No action");
			$success = false;
		}

		if (!array_key_exists ('climbs', $_GET)) {
			climb_add_error ($xml, "No climbs");
			$success = false;
		} else {
			$climbs = trim ($_GET['climbs']);
			if (empty ($climbs)) {
				climb_add_error ($xml, "Empty climbs");
				$success = false;
			}
		}

		if (!array_key_exists ('climber', $_GET)) {
			climb_add_error ($xml, "No climber");
			$success = false;
		}
	}

	return $success;
}

function climb_lookup_climber (&$xml, $name)
{
	static $g_climbers = null;
	global $DB_CLIMBER;

	if ($g_climbers === null) {
		$columns = array ('id', 'first_name', 'surname', 'trim(concat(first_name, " ", surname)) as name');
		$g_climbers = db_select ($DB_CLIMBER, $columns);
		if ($g_climbers === null)
			return null;
	}

	foreach ($g_climbers as $c) {
		if (strcasecmp ($c['name'], $name) == 0) {
			return $c['id'];
		}
	}

	climb_add_error ($xml, sprintf ("'%s' is not a valid climber", $name));
	return null;
}

function climb_valid_date (&$xml, $date)
{
	$t = strtotime ('today');
	$d = strtotime ($date);
	if ($d === false) {
		climb_add_error ($xml, sprintf ("'%s' is not a valid date", $date));
		return null;
	}

	if ($d > $t) {
		climb_add_error ($xml, sprintf ("'%s': Date cannot be in the future", $date));
		return null;
	}

	return strftime('%Y-%m-%d', $d);
}


function climb_parse_climb (&$xml, $text)
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
		$c = colour_match ($colour);
		if ($c === null) {
			climb_add_error ($xml, sprintf ("'%s' is not a valid colour\n", $colour));
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
			climb_add_error ($xml, sprintf ("Bad multiple: %s\n", $n));
			return null;
		}

		if ($note == 'f') {
			$type = "fall";
		} else if ($note == 'r') {
			$type = "rest";
		} else {
			climb_add_error ($xml, sprintf ("Unknown type: %s\n", $note));
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


function climb_add_climb()
{
}

function climb_commit_climb (&$xml, $climbs)
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

	$result = mysql_query($query);
	if ($result === true) {
		climb_add_error ($xml, sprintf ("Created %d climbs", mysql_affected_rows()));
		//printf ("Created %d climbs\n\n", mysql_affected_rows());
		return mysql_affected_rows();
	} else {
		return -1;
	}
}

function climb_commit_rating (&$xml, $ratings)
{
	// split the ratings into "insert" or "update"

	$rids = array();
	foreach ($ratings as $r) {
		$route_id = $r['route_id'];
		$rids[$route_id] = $route_id;
	}

	$rating_ids = climb_find_rating_ids ($rids);

	$count_insert = 0;
	$count_update = 0;
	$values_insert = array();
	$values_update = array();
	foreach ($ratings as $r) {
		if (($r['nice'] == 'nice') || ($r['nice'] == '1'))
			$r['nice'] = 1;
		else
			$r['nice'] = 0;

		if (empty ($r['difficulty'])) {
			$r['difficulty'] = 'null';
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

	//climb_add_error ($xml, print_r ($values_insert, true));
	if (count ($values_insert) > 0) {
		$query  = "insert into rating (climber_id, route_id, difficulty_id, notes, nice) values ";
		$values = array();
		foreach ($values_insert as $r) {
			$values[] = '(' .
					$r['climber_id']    . ',' .
					$r['route_id']      . ',' .
					$r['difficulty']    . ',' .
				"'" .	$r['notes'] . "'"   . ',' .
					$r['nice']          .
				')';
		}

		$query .= implode (',', $values);

		$result = mysql_query($query);
		if ($result === true) {
			climb_add_error ($xml, sprintf ("Created %s new ratings", mysql_affected_rows()));
		} else {
			climb_add_error ($xml, "Rating insert failed");
		}
	}

	if (count ($values_update) > 0) {
		foreach ($values_update as $r) {
			$query = "update rating set ";
			$query .= "climber_id    = " . $r['climber_id']    . ',';
			$query .= "route_id      = " . $r['route_id']      . ',';
			$query .= "difficulty_id = " . $r['difficulty_id'] . ',';
			$query .= "nice          = " . $r['nice']          . ',';
			$query .= "notes         = '" . $r['notes']        . "' ";

			$query .= "where id = " . $r['rating_id'];

			$result = mysql_query($query);
			if ($result === true) {
				$count_update++;
			} else {
				climb_add_error ($xml, "Rating update failed");
			}
		}

		if ($count_update > 0) {
			climb_add_error ($xml, sprintf ("Updated %s existing ratings", $count_update));
		}
	}
}


function climb_commit_notes (&$xml, &$ratings)
{
	foreach ($ratings as $r) {
		printf ("%-5s %-40s %-20s\n", $r['climb_note_id'], $r['climb_note'], $r['notes']);
	}

	//var_dump ($ratings);
	exit (1);

	// Have route id, notes
	//
	/*
		Original	New
		Rating		Rating		To do
	   ---------------------------------------------
		   --		   --		nothing

		   --		NEW UNIQ	create note
						add id to rating

		   --		NEW SHARED	use existing id
						add id to rating

		OLD UNIQ	   --		delete note
						add null to rating

		OLD SHARED	   --		do nothing
						add null to rating

		OLD UNIQ	OLD UNIQ	do nothing

		OLD SHARED	OLD SHARED	do nothing

		OLD UNIQ	NEW UNIQ	overwrite note
						add id to rating

		OLD UNIQ	NEW SHARED	delete note
						add id to rating

		OLD SHARED	NEW UNIQ	create note
						add id to rating

		OLD SHARED	NEW SHARED	use existing id
						add id to rating

	*/

	$count = 0;
	foreach ($ratings as &$r) {
		$n = trim ($r['notes']);
		if (empty ($n)) {
			$r['climb_note_id'] = null;
			continue;
		}

		$query = "insert into climb_note set notes = '$n'";
		$result = mysql_query($query);
		if ($result === true) {
			$r['climb_note_id'] = mysql_insert_id();
			$count++;
		} else {
			climb_add_error ($xml, "add note failed");
			$r['climb_note_id'] = null;
		}
	}
	if ($count > 0) {
		climb_add_error ($xml, sprintf ("Created %d notes", $count));
		printf ("Created %d notes\n\n", $count);
	}

	return $count;
}

function climb_get_rating_notes (&$xml, &$climbs)
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

	$ratings = db_select ($table, $columns, $where);

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


function climb_do_add (&$xml)
{
	if (!array_key_exists ('date', $_GET)) {
		climb_add_error ($xml, "No date");
		return;
	}

	$date = climb_valid_date ($xml, $_GET['date']);
	if ($date === null)
		return;

	if (!array_key_exists ('climbs', $_GET)) {
		climb_add_error ($xml, "No climbs");
		return;
	} else {
		$climbs = trim ($_GET['climbs']);
		if (empty ($climbs)) {
			climb_add_error ($xml, "Empty climbs");
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

	$panel = climb_get_panels (array ($panel_name));
	if (count ($panel) == 0) {
		climb_add_error ($xml, sprintf ("'%s' is not a valid panel name", $name));
		return;
	}

	$climbs = array();
	$errors = 0;
	foreach ($parts as $colour) {
		$p = climb_parse_climb ($xml, $colour);
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

		climb_add_error ($xml, sprintf ("Panel '%s' doesn't have a '%s' route", $panel, $p['colour']));
		$errors++;
	}

	if ($errors > 0)
		return;

	climb_get_rating_notes ($xml, $climbs);

	foreach ($climbs as $c) {
		$climb = $xml->addChild ('route');
		$climb->addChild ('climb_type', $c['climb_type']);
		$climb->addChild ('colour',     $c['colour']);
		$climb->addChild ('date',       $date);
		$climb->addChild ('difficulty', $c['difficulty']);
		$climb->addChild ('grade',      $c['grade']);
		$climb->addChild ('id',         $c['id']);
		$climb->addChild ('nice',       $c['nice']);
		$climb->addChild ('notes',      $c['notes']);
		$climb->addChild ('panel',      $c['panel']);
		$climb->addChild ('success',    $c['success']);
	}
}

function climb_do_save (&$xml)
{
	global $_GET;

	if (!array_key_exists ('climb_xml', $_GET)) {
		climb_add_error ($xml, "No climb_xml");
		return;
	} else {
		$climbs = trim ($_GET['climb_xml']);
		if (empty ($climbs)) {
			climb_add_error ($xml, "Empty climbs");
			return;
		}
	}

	if (!array_key_exists ('climber', $_GET)) {
		climb_add_error ($xml, "No climber");
		return;
	}
	$climber_id = climb_lookup_climber ($xml, $_GET['climber']);
	if ($climber_id === null)
		return;

	$cxml = simplexml_load_string ($climbs);

	//printf ("committing %d climbs for %s\n", $cxml->count(), $_GET['climber']);

	// Collect the panel names
	$pnames = array();
	for ($i = 0; $i < $cxml->count(); $i++) {
		$p = (string) $cxml->climb[$i]->panel;
		$pnames[$p] = $p;
	}

	$panels = climb_get_panels ($pnames);

	climb_get_success();
	climb_get_difficulty();

	$commit_climb = array();
	$commit_rating = array();
	//printf ("\n");
	//printf ("rid   Panel Colour         Date         Success     Diff       Nice   Notes\n");
	for ($i = 0; $i < $cxml->count(); $i++) {
		$a = $cxml->climb[$i];
		$route_id = climb_get_route_id ($xml, $panels, $a->panel, $a->colour);
		if ($route_id === null) {
			climb_add_error ($xml, sprintf ("Route %s %s doesn't exist", $a->panel, $a->colour));
			continue;
		}
		$date_climbed = climb_valid_date ($xml, $a->date);
		$success_id = climb_lookup_success ($a->success);
		$difficulty_id = climb_lookup_difficulty ($a->difficulty);
		$nice = (string) $a->nice;
		$notes = (string) $a->notes;

		//printf ("%-5s %-5s %-14s %-12s %-11s %-10s %-6s %s\n", $route_id, $a->panel, $a->colour, $a->date, $a->success, $a->difficulty, $a->nice, $a->notes);

		// Validate add_climb
		// Add climb using: climber_id, route_id, success_id, date_climbed
		$c = array();
		$c['climber_id']   = $climber_id;
		$c['route_id']     = $route_id;
		$c['success_id']   = $success_id;
		$c['date_climbed'] = $date_climbed;
		$commit_climb[] = $c;

		// Validate add rating
		// Create new rating using: climber_id, route_id, difficulty_id, climb_note_id, nice
		$r = array();
		$r['climber_id']    = $climber_id;
		$r['route_id']      = $route_id;
		$r['difficulty_id'] = $difficulty_id;
		$r['nice']          = $nice;
		$r['notes']         = $notes;
		$commit_rating[] = $r;
	}
	//printf ("\n");

	climb_commit_climb ($xml, $commit_climb);
	climb_commit_rating ($xml, $commit_rating);

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
		climb_add_error ($xml, "No action");
		return;
	}

	$action  = $_GET['action'];

	switch ($action) {
	case 'add':
		climb_do_add ($xml);
		break;
	case 'save':
		climb_do_save ($xml);
		break;
	default:
		climb_add_error ($xml, sprintf ("'%s' is not a valid action", $action));
		break;
	}
}

function get_random_climbs()
{
	$nice1 = (rand (0,3) == 0) ? "nice" : "";
	$nice2 = (rand (0,6) == 0) ? "nice" : "";
	$nice3 = (rand (0,9) == 0) ? "nice" : "";

	$diffs = array ('very easy', 'easy', 'medium', 'hard', 'very hard');

	$diff1 = $diffs[rand(0,4)];
	$diff2 = $diffs[rand(0,4)];
	$diff3 = $diffs[rand(0,4)];

	$notes1 = sprintf ("notes %d blah", rand (0,9));
	$notes2 = sprintf ("notes %d blah", rand (0,9));
	$notes3 = sprintf ("notes %d blah", rand (0,9));

	if (rand (0,3) == 0) $notes1 = null;
	if (rand (0,5) == 0) $notes2 = null;
	if (rand (0,7) == 0) $notes3 = null;

	$date_base = strtotime ("2007-01-01");
	$date_ref  = strtotime ("2011-02-22");
	$date_now  = strtotime ("now");

	$date = $date_base + (($date_now - $date_ref) * 1440);	// 1 day per minute after ref
	$datestr = strftime('%Y-%m-%d', $date);

	$successes = array ("failed", "success", "clean", "downclimb");

	$succ1 = $successes[rand (0,3)];
	$succ2 = $successes[rand (0,3)];
	$succ3 = $successes[rand (0,3)];

	$xml = "<list type='climb'>";

	$xml .= sprintf ("<climb><panel>3</panel><colour>Orange</colour><date>%s</date><success>%s</success><difficulty>%s</difficulty><nice>%s</nice><notes>%s</notes></climb>",
		$datestr, $succ1, $diff1, $nice1, $notes1);
	$xml .= sprintf ("<climb><panel>3</panel><colour>Purple/White</colour><date>%s</date><success>%s</success><difficulty>%s</difficulty><nice>%s</nice><notes>%s</notes></climb>",
		$datestr, $succ2, $diff2, $nice2, $notes2);
	$xml .= sprintf ("<climb><panel>4</panel><colour>Purple</colour><date>%s</date><success>%s</success><difficulty>%s</difficulty><nice>%s</nice><notes>%s</notes></climb>",
		$datestr, $succ3, $diff3, $nice3, $notes3);

	$xml .= "</list>";

	return $xml;
}

function test_data()
{
	global $_GET;

	if (0) {
		$_GET['action']  = "add";
		$_GET['date']    = '2 days ago';
		$_GET['climbs']  = "3 or(d) p/w(dn), blu(mf)";
		//$_GET['climbs']  = "46 pw(d), blu, bg(2f), fe(f)";
		//$_GET['climbs']  = "32 all(d)";
		//$_GET['climbs']  = "71 all";
	} else {
		$_GET['action']  = "save";
		$_GET['climb_xml']  = get_random_climbs();
		$_GET['climber'] = "Rich Russon";
	}

}

function test_parse()
{
	$c = array ("pw", "rd (c)", "ti (s) ", "f(f) ", "tq (d)", "gray ( 1r)", "pk ( 1f  )", "r/w ( n)", "y ( mf  ) ", "all", "all(d)");

	printf ("abbr        colour        success     nice    notes\n");
	foreach ($c as $climb) {
		$p = climb_parse_climb ($xml, $climb);
		printf ("%-12s%-14s%-12s%-8s%s\n", $climb, $p['colour'], $p['success'], $p['nice'], $p['notes']);
	}
}


header('Content-Type: application/xml; charset=ISO-8859-1');
$xml = new SimpleXMLElement ("<?xml-stylesheet type='text/xsl' href='route.xsl'?"."><list />");
$xml->addAttribute ('type', 'climb');

if (isset ($argc))
	test_data();
climb_main ($xml);

echo $xml->asXML();

