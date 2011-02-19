<?php

set_include_path ('../../libs');

include 'colour.php';
include 'db.php';
include 'db_names.php';

date_default_timezone_set('UTC');

global $g_climbers;
global $g_panels;

function climb_add_error (&$xml, $message)
{
	$xml->addChild ('error', $message);
}


function climb_get_climbers()
{
	global $DB_CLIMBER;
	global $g_climbers;

	$table   = $DB_CLIMBER;
	$columns = array ('id', 'first_name', 'surname', 'trim(concat(first_name, " ", surname)) as name');

	$g_climbers = db_select ($table, $columns);
}

function climb_get_panels()
{
	global $DB_V_ROUTE;
	global $g_panels;

	$table   = $DB_V_ROUTE;
	$columns = array ('id', 'panel', 'colour', 'grade', 'climb_type');
	$where   = null;
	$order   = "panel_seq, grade_seq, colour";

	$g_panels = db_select ($table, $columns, $where, $order);
}

function climb_get_all_panel ($name)
{
	global $g_panels;

	$results = array();
	foreach ($g_panels as $p) {
		if ($p['panel'] == $name) {
			$results[] = $p;
		}
	}

	return $results;
}

function climb_get_notes ($panel)
{
	global $DB_CLIMB_NOTE;
	global $DB_DIFFICULTY;
	global $DB_PANEL;
	global $DB_RATING;
	global $DB_ROUTE;

	$table   = $DB_PANEL .
			" left join $DB_ROUTE      on ($DB_ROUTE.panel_id       = $DB_PANEL.id)" .
			" left join $DB_RATING     on ($DB_RATING.route_id      = $DB_ROUTE.id)" .
			" left join $DB_DIFFICULTY on ($DB_RATING.difficulty_id = $DB_DIFFICULTY.id)" .
			" left join $DB_CLIMB_NOTE on ($DB_RATING.climb_note_id = $DB_CLIMB_NOTE.id)";

	$columns = array ("$DB_ROUTE.id               as id",
			  "$DB_PANEL.name             as panel",
			  "$DB_DIFFICULTY.description as difficulty",
			  "$DB_RATING.nice            as nice",
			  "$DB_CLIMB_NOTE.notes       as notes");

	$where   = array ("$DB_PANEL.name = '$panel'");

	$list = db_select($table, $columns, $where);

	return $list;
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

		/*
		if (!array_key_exists ('date', $_GET)) {
			climb_add_error ($xml, "No date");
			$success = false;
		}
		*/

		if (!array_key_exists ('climber', $_GET)) {
			climb_add_error ($xml, "No climber");
			$success = false;
		}
	}

	return $success;
}

function climb_valid_climber (&$xml, $name)
{
	global $g_climbers;

	foreach ($g_climbers as $c) {
		if (strcasecmp ($c['name'], $name) == 0) {
			return $c['id'];
		}
	}

	climb_add_error ($xml, sprintf ("'%s' is not a valid climber", $name));
	return null;
}

function climb_valid_panel (&$xml, $name)
{
	global $g_panels;

	foreach ($g_panels as $p) {
		if (strcasecmp ($p['panel'], $name) == 0) {
			return $p['id'];
		}
	}

	climb_add_error ($xml, sprintf ("'%s' is not a valid panel name", $name));
	return null;
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


function climb_do_add (&$xml)
{
	$climbs  = $_GET['climbs'];
	$date    = $_GET['date'];
	$climber = $_GET['climber'];

	$t = strtotime ('today');
	$d = strtotime ($date);
	if ($d === false) {
		climb_add_error ($xml, sprintf ("'%s' is not a valid date", $date));
		return;
	}

	if ($d > $t) {
		climb_add_error ($xml, sprintf ("'%s': Date cannot be in the future", $date));
		return;
	}

	$date = strftime('%Y-%m-%d', $d);

	climb_get_climbers();
	$climber_id = climb_valid_climber ($xml, $climber);
	if ($climber_id === null) {
		return;
	}

	$parts = preg_split('/[\s,]+/', $climbs);
	$panel = array_shift ($parts);
	foreach ($parts as $key => $p) {
		if ($p == '') {
			unset ($parts[$key]);
		}
	}

	climb_get_panels();
	$panel_id = climb_valid_panel ($xml, $panel);
	if ($panel_id === null) {
		return;
	}

	$climbs = array();
	$errors = 0;
	$all = climb_get_all_panel ($panel);
	foreach ($parts as $colour) {
		$p = climb_parse_climb ($xml, $colour);
		if ($p === null) {
			$errors++;
			continue;
		}

		if ($p['colour'] == 'all') {
			foreach ($all as $a) {
				$p['colour'] = $a['colour'];
				$climbs[] = array_merge ($a, $p);
			}
			continue;
		}

		// check that the colours actually exist
		foreach ($all as $a) {
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

	$notes = climb_get_notes ($panel);

	foreach ($climbs as $c) {
		$climb = $xml->addChild ('route');
		$climb->addChild ('panel', $c['panel']);
		$climb->addChild ('id', $c['id']);
		$climb->addChild ('colour', $c['colour']);
		$climb->addChild ('climb_type', $c['climb_type']);
		$climb->addChild ('grade', $c['grade']);
		$climb->addChild ('date', $date);

		$id = $c['id'];
		if (array_key_exists ($id, $notes)) {
			$n1 = $notes[$id]['notes'];
			$n2 = $c['notes'];
			if (!empty ($n1) && !empty ($n2)) {
				$n1 = "$n1; $n2";
			} else {
				$n1 = trim ("$n1 $n2");
			}
			$c['notes']      = $n1;
			$c['difficulty'] = $notes[$id]['difficulty'];
			if (($c['nice'] === true) || ($notes[$id]['nice'] == '1'))
				$c['nice'] = 'nice';
		}

		$climb->addChild ('success', $c['success']);
		$climb->addChild ('nice', $c['nice']);
		$climb->addChild ('notes', $c['notes']);
		$climb->addChild ('difficulty', $c['difficulty']);
	}
}

function climb_do_save (&$xml)
{
	$climbs  = $_GET['climbs'];
	$climber = $_GET['climber'];

	$cxml = simplexml_load_string ($climbs);

	$count = $cxml->count();
	climb_add_error ($xml, sprintf ("all ok, %d children", $count));
	//climb_add_error ($xml, $cxml->asXML());

	for ($i = 0; $i < $cxml->count(); $i++) {
		$a = $cxml->climb[$i];
		climb_add_error ($xml, sprintf ("child: %s", $a->colour));
		climb_add_error ($xml, sprintf ("child: %s", urldecode ($a->type)));
		//climb_add_error ($xml, sprintf ("child: %s", $climbs));
	}

	// for each climb
	//	convert <panel> <colour>		route_id
	//	parse and validate <date>		date
	//	parse and validate <success>		success_id
	//	parse and validate <difficulty>		difficulty_id
	//	parse and validate <nice>		nice
	//	parse the <notes>			notes

	// Add climb  using: climber_id, route_id, success_id, date_climbed

	// Does rating exist?
	// Yes:
	//	Does rating have a climb_note?
	//	Yes:
	//		Is the climb_note unique to this rating?
	//		Yes:
	//			Update note using: notes
	//		No:
	//			COW, create a new note using: notes
	//	No:
	//		Create a new note using: notes
	// No:
	//	Does note already exist?
	//	Yes:
	//		Use existing note
	//	No:
	//		Create new note using: notes
	//	Create new rating using: climber_id, route_id, difficulty_id, climb_note_id, nice
}


function climb_main (&$xml)
{
	global $_GET;

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


if (0) {
	//$_GET['climbs']  = "46 pw(d), blu, bg(2f), fe(f)";
	//$_GET['climbs']  = "32 all(d)";
	$_GET['climbs']  = '<list type="climb"><climb><tick>false</tick><panel>3</panel><colour>Orange</colour><grade>5+</grade><type>Top Rope</type><date>2011-02-19</date><success>downclimb</success></climb><climb><tick>false</tick><panel>3</panel><colour>Purple/White</colour><grade>5+</grade><type>Top Rope</type><date>2011-02-19</date><success>downclimb</success></climb><climb><tick>false</tick><panel>3</panel><colour>Blue</colour><grade>6b+</grade><type>Top Rope</type><date>2011-02-19</date><success>failed</success></climb></list>';
	//$_GET['climbs']  = "71 all";
	//$_GET['action']  = "add";
	$_GET['action']  = "save";
	$_GET['climber'] = "Rich Russon";
	//$_GET['date']    = '2 days ago';
}

header('Content-Type: application/xml; charset=ISO-8859-1');
$xml = new SimpleXMLElement ("<?xml-stylesheet type='text/xsl' href='route.xsl'?"."><list />");
$xml->addAttribute ('type', 'climb');

climb_main ($xml);

echo $xml->asXML();

/*
$c = array ("pw", "rd (c)", "ti (s) ", "f(f) ", "tq (d)", "gray ( 1r)", "pk ( 1f  )", "r/w ( n)", "y ( mf  ) ", "all", "all(d)");

printf ("abbr        colour        success     nice    notes\n");
foreach ($c as $climb) {
	$p = climb_parse_climb ($xml, $climb);
	printf ("%-12s%-14s%-12s%-8s%s\n", $climb, $p['colour'], $p['success'], $p['nice'], $p['notes']);
}

*/
