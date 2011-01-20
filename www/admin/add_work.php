<?php

date_default_timezone_set('UTC');

set_include_path ('../../libs');

include 'db.php';
include 'db_names.php';
include 'cache.php';
include 'utils.php';

function colours_process ($colours)
{
	$lookup = array();

	foreach ($colours as $ckey => $c) {
		$lookup[strtolower ($c['colour'])] = &$colours[$ckey];
		$abbr = explode (',', $c['abbr']);
		foreach ($abbr as $a) {
			$lookup[$a] = &$colours[$ckey];
		}
		unset ($colours[$ckey]['abbr']);
	}

	return $lookup;
}

function colours_match_single ($lookup, $test)
{
	if (array_key_exists ($test, $lookup))
		return $lookup[$test]['id'];
	else
		return null;
}

function colours_match ($lookup, $test)
{
	global $g_colours;

	$test = strtolower ($test);

	$id = colours_match_single ($lookup, $test);
	if ($id !== null)
		return $id;

	$pos = strpos ($test, '/');
	if ($pos === false)
		return $id;

	$id1 = colours_match_single ($lookup, substr($test, 0, $pos));
	$id2 = colours_match_single ($lookup, substr($test, $pos+1));

	if (($id1 === null) || ($id2 === null))
		return null;

	$col1 = $g_colours[$id1]['colour'];
	$col2 = $g_colours[$id2]['colour'];

	$test = strtolower ($col1.'/'.$col2);
	$id = colours_match_single ($lookup, $test);

	return $id;
}


function parse_colour ($text)
{
	global $DB_COLOUR;
	static $colours = null;
	static $lookup  = null;

	if (!$colours)
		$colours = cache_get_table ($DB_COLOUR);
	if (!$lookup)
		$lookup = colours_process ($colours);

	$id = colours_match ($lookup, $text);
	if ($id !== null)
		return $colours[$id];
	else
		return null;
}

function parse_grade ($text)
{
	global $DB_GRADE;
	static $grades = null;

	if (!$grades)
		$grades = cache_get_table ($DB_GRADE);

	foreach ($grades as $g) {
		if ($text == $g['grade'])
			return $g;
	}

	return null;
}

function parse_panel ($text)
{
	global $DB_PANEL;
	static $panels = null;

	if (!$panels)
		$panels = cache_get_table ($DB_PANEL);

	foreach ($panels as $p) {
		if ($text == $p['name'])
			return $p;
	}

	return null;
}

function parse_setter ($text)
{
	// Need to match:
	//     initials
	//     unique first_name
	//     unique surname
	//     first_name surname

	global $DB_SETTER;
	static $setters = null;

	if (!$setters)
		$setters = cache_get_table ($DB_SETTER);

	$text = strtolower ($text);
	foreach ($setters as $s) {
		$name = strtolower ($s['first_name'] . ' ' . $s['surname']);
		if ($text == $name)
			return $s;
		if ($text == strtolower ($s['initials']))
			return $s;
	}

	return null;
}


function valid_colour (&$route)
{
	$c = parse_colour ($route->colour);
	if ($c !== null) {
		$route->colour = $c['colour'];
		$route->colour_id = $c['id'];
		return true;
	} else {
		$route->addChild ('message', sprintf ("'%s' is not a valid colour", $route->colour));
		return false;
	}
}

function valid_date (&$route)
{
	$d = strtotime ($route->date);

	if ($d !== false) {
		$now = strtotime ('now');
		if ($d <= $now) {
			$route->date = strftime('%Y-%m-%d', $d);
			return true;
		}
		$route->addChild ('message', 'Date cannot be in the future');
	} else {
		$route->addChild ('message', sprintf ("'%s' is not a valid date", $route->date));
	}

	return false;
}

function valid_grade (&$route)
{
	$g = parse_grade ($route->grade);
	if ($g !== null) {
		$route->grade    = $g['grade'];
		$route->grade_id = $g['id'];
		return true;
	} else {
		$route->addChild ('message', sprintf ("'%s' is not a valid grade", $route->grade));
		return false;
	}
}

function valid_notes (&$route)
{
	// check for invalid characters
	return true;
}

function valid_panel (&$route)
{
	$p = parse_panel ($route->panel);
	if ($p !== null) {
		$route->panel    = $p['name'];
		$route->panel_id = $p['id'];
		return true;
	} else {
		$route->addChild ('message', sprintf ("'%s' is not a valid panel", $route->panel));
		return false;
	}
}

function valid_setter (&$route)
{
	$s = parse_setter ($route->setter);
	if ($s !== null) {
		$route->setter    = $s['first_name'] . ' ' . $s['surname'];
		$route->setter_id = $s['id'];
		return true;
	} else {
		$route->addChild ('message', sprintf ("'%s' is not a valid setter", $route->setter));
		return false;
	}
}


function validate_route (&$route)
{
	$colour = valid_colour ($route);
	$date   = valid_date   ($route);
	$grade  = valid_grade  ($route);
	$notes  = valid_notes  ($route);
	$panel  = valid_panel  ($route);
	$setter = valid_setter ($route);

	return ($colour && $date && $grade && $notes && $panel && $setter);
}

function db_route_add ($route)
{
	/*
	printf ("panel   = %s (%d)\n", $route->panel,  $route->panel_id);
	printf ("colour  = %s (%d)\n", $route->colour, $route->colour_id);
	printf ("grade   = %s (%d)\n", $route->grade,  $route->grade_id);
	printf ("setter  = %s (%d)\n", $route->setter, $route->setter_id);
	printf ("date    = %s\n",      $route->date);
	printf ("notes   = %s\n",      $route->notes);
	*/
	return rand (101,200);
}


function route_add ($data)
{
	//printf ("data = >>$data<<\n");
	// parse:
	//	45 red 5+, gn 6a, bg 6b into
	// into
	//	45 Red 5+
	//	45 Green 6a
	//	45 Beige 6b

	$routes = array();

	$list = explode (' ', $data, 2);

	$panel = $list[0];
	$data  = $list[1];

	$list = explode (',', $data);
	foreach ($list as $item) {
		$item = trim ($item);
		list ($colour, $grade) = explode (' ', $item, 2);
		$colour = colour_parse ($colour);
		$grade = trim ($grade);
		$routes[] = array ('panel' => $panel, 'colour' => $colour, 'grade' => $grade);
	}

	$columns = array ('panel', 'colour', 'grade');
	$xml = '<?xml-stylesheet type="text/xsl" href="route.xsl"?'.'>';
	$xml .= list_render_xml ('route', $routes, $columns);
	echo $xml;
}


function route_save ($data)
{
	$xml = simplexml_load_string ($data);

	for ($i = 0; $i < $xml->count(); $i++) {
		$message = array();
		$a = $xml->route[$i];
		$route = validate_route ($a);
		if ($route !== false) {
			$route_id = db_route_add ($a);
		} else {
			$route_id = false;
		}

		if ($route_id) {
			//add attribute
			$xml->route[$i]->addAttribute ('result', 'success');
			$xml->route[$i]->addChild ('route_id', $route_id);
		} else {
			//add attribute
			$xml->route[$i]->addAttribute ('result', 'failure');
		}
	}

	return $xml->asXML();
}

function route_main()
{
	global $_GET;

	if (!isset ($_GET)) {
		echo 'NO GET';
		return;
	}

	if (!array_key_exists ('action', $_GET)) {
		echo 'NO ACTION';
		return;
	}
	$action = $_GET['action'];

	if (array_key_exists ('data', $_GET)) {
		$data = $_GET['data'];
	} else {
		$data = '';
	}

	switch ($action) {
		case 'add':
			header('Content-Type: application/xml; charset=ISO-8859-1');
			$response = route_add($data);
			break;
		case 'save':
			$response = route_save($data);
			break;
		default:
			$response = "unknown action";
			break;
	}

	return $response;
}


//$_GET = array('action' => 'add', 'data' => '45   red  5+,     gn   6a  ,  bg   6b  ');
if (isset ($argc)) {
} else {
	header('Content-Type: application/xml; charset=ISO-8859-1');
	//echo "<pre>";
}

$xml = file_get_contents ('route.xml');
$_GET = array('action' => 'save', 'data' => $xml);
$result = route_main();
//$result = htmlentities ($result);
echo $result;

