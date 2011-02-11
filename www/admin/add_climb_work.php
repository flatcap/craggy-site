<?php

set_include_path ('../../libs');

include 'colour.php';

date_default_timezone_set('UTC');

function add_error ($xml, $message)
{
	$xml->addChild ('error', $message);
}

function valid_command ($xml)
{
	global $_GET;

	$success = true;

	if (!isset ($_GET)) {
		add_error ($xml, "No get");
		$success = false;
	} else {
		if (!array_key_exists ('action', $_GET)) {
			add_error ($xml, "No action");
			$success = false;
		}

		if (!array_key_exists ('climbs', $_GET)) {
			add_error ($xml, "No climbs");
			$success = false;
		}

		$climbs = trim ($_GET['climbs']);
		if (empty ($climbs)) {
			add_error ($xml, "Empty climbs");
			$success = false;
		}

		if (!array_key_exists ('date', $_GET)) {
			add_error ($xml, "No date");
			$success = false;
		}

		if (!array_key_exists ('climber', $_GET)) {
			add_error ($xml, "No climber");
			$success = false;
		}
	}

	return $success;
}

function parse_climb ($xml, $text)
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

	$colour = $parts[0];
	if (array_key_exists (1, $parts))
		$note = strtolower ($parts[1]);
	else
		$note = "";

	$c = colour_match ($colour);
	if ($c === null) {
		printf ("'%s' is not a valid colour\n", $colour);
		return null;
	}

	$result['colour'] = $c['colour'];
	//printf ("colour = %s\n", $colour);

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
			printf ("Bad multiple: %s\n", $n);
			return null;
		}

		if ($note == 'f') {
			$type = "fall";
		} else if ($note == 'r') {
			$type = "rest";
		} else {
			printf ("Unknown type: %s\n", $note);
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

function climb_main ($xml)
{
	global $_GET;

	$action  = $_GET['action'];
	$climbs  = $_GET['climbs'];
	$date    = $_GET['date'];
	$climber = $_GET['climber'];

	if ($action != "add") {
		add_error ($xml, sprintf ("'%s' is not a valid action", $action));
		return;
	}

	$t = strtotime ('today');
	$d = strtotime ($date);
	if ($d === false) {
		add_error ($xml, sprintf ("'%s' is not a valid date", $date));
		return;
	}

	if ($d > $t) {
		add_error ($xml, sprintf ("'%s': Date cannot be in the future", $date));
		return;
	}

	$date = strftime('%Y-%m-%d', $d);

	if ($climber != "Rich Russon") {
		add_error ($xml, sprintf ("'%s' is not a valid climber", $climber));
		return;
	}

	$parts = preg_split('/[\s,]+/', $climbs);
	$panel = array_shift ($parts);
	foreach ($parts as $key => $p) {
		if ($p == '') {
			unset ($parts[$key]);
		}
	}

	if (!is_numeric ($panel)) {
		// Craggy specific - need to compare against panel name
		add_error ($xml, sprintf ("'%s' is not a valid panel name", $panel));
		return;
	}

	foreach ($parts as $colour) {
		printf ("colour = %s\n", $colour);
		$climb = $xml->addChild ('route');
		$climb->addChild ('panel', $panel);

		$c = colour_match ($colour);
		if ($c !== null) {
			$climb->addAttribute ('result', 'valid');
			$climb->addChild ('colour', $c['colour']);
		} else {
			$climb->addAttribute ('result', 'invalid');
			$climb->addChild ('colour', $colour);
			add_error ($climb, sprintf ("%s is not a valid colour", $colour));
		}

		$climb->addChild ('grade', '6a+');			// grade
		$climb->addChild ('success', 'clean');			// success
		$climb->addChild ('difficulty', 'medium');		// difficulty
		$climb->addChild ('climb_type', 'Top Rope');		// type
		$climb->addChild ('nice', 'N');				// nice
		$climb->addChild ('onsight', 'O');			// onsight
		$climb->addChild ('date', $date);			// date_climbed
		$climb->addChild ('notes', 'tricky');			// notes - climb_notes

	}
	exit (1);

}


$_GET['climbs']  = "46 pw(d), blu, bg(s), fe(f)";
$_GET['action']  = "add";
$_GET['climber'] = "Rich Russon";
$_GET['date']    = '2 days ago';

header('Content-Type: application/xml; charset=ISO-8859-1');
$xml = new SimpleXMLElement ("<?xml-stylesheet type='text/xsl' href='route.xsl'?"."><list />");
$xml->addAttribute ('type', 'climb');

/*
if (valid_command ($xml)) {
	climb_main ($xml);
}

echo $xml->asXML();
*/

$c = array ("pw", "rd (c)", "ti (s) ", "f(f) ", "tq (d)", "gray ( 1r)", "pk ( 1f  )", "r/w ( n)", "y ( mf  ) "); 

printf ("abbr        colour        success     nice    notes\n");
foreach ($c as $climb) {
	$p = parse_climb ($xml, $climb);
	printf ("%-12s%-14s%-12s%-8s%s\n", $climb, $p['colour'], $p['success'], $p['nice'], $p['notes']);
}

