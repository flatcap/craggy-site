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

function colour_parse2 ($text)
{
	global $DB_COLOUR;
	static $colours = null;
	static $lookup  = null;

	if (!$colours)
		$colours = cache_get_table ($DB_COLOUR);
	if (!$lookup)
		$lookup = colours_process ($colours);

	$id = colours_match ($lookup, $text);
	return $colours[$id];
}


function valid_colour ($text)
{
	return $g_colours[$id];
}

function valid_date ($text)
{
	// valid or empty
	return $g_dates[$id];
}

function valid_grade ($text)
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

function valid_panel ($text)
{
	return $g_panels[$id];
}

function valid_setter ($text)
{
	return $g_setters[$id];
}


function parse_colour ($text, &$message)
{
	global $DB_COLOUR;
	static $colours = null;

	if (!$colours)
		$colours = cache_get_table ($DB_COLOUR);

	foreach ($colours as $c) {
		if ($text == $c['colour'])
			return $c;
	}

	$message[] = sprintf ("'%s' is not a valid colour", $text);
	return null;
}

function parse_date ($text, &$message)
{
	$time = strtotime ($text);

	if ($time === null) {
		$message[] = sprintf ("'%s' is not a valid date", $text);
	}

	return $time;
}

function parse_grade ($route)
{
	$text = $route->grade;
	$g = valid_grade ($text);
	if ($g !== null) {
		$route->grade = $g['grade'];
		return true;
	} else {
		$route->addChild ('message', sprintf ("'%s' is not a valid grade", $text));
		return false;
	} else {
	}

}

function parse_panel ($text, &$message)
{
	global $DB_PANEL;
	static $panels = null;

	if (!$panels)
		$panels = cache_get_table ($DB_PANEL);

	foreach ($panels as $p) {
		if ($text == $p['name'])
			return $p;
	}

	$message[] = sprintf ("'%s' is not a valid panel", $text);
	return null;
}

function parse_setter ($text, &$message)
{
	global $DB_SETTER;
	static $setters = null;

	if (!$setters)
		$setters = cache_get_table ($DB_SETTER);

	foreach ($setters as $s) {
		$name = $s['first_name'] . ' ' . $s['surname'];
		if ($text == $name)
			return $s;
	}

	$message[] = sprintf ("'%s' is not a valid setter", $text);
	return null;
}


function validate_route ($route, &$message)
{
	/*
	printf ("colour = %s\n", $route->colour);
	printf ("date   = %s\n", $route->date);
	printf ("grade  = %s\n", $route->grade);
	printf ("panel  = %s\n", $route->panel);
	printf ("setter = %s\n", $route->setter);
	printf ("notes  = %s\n", $route->notes);
	printf ("\n");
	*/

	$colour = parse_colour ($route->colour);
	$date   = parse_date   ($route->date);
	$grade  = parse_grade  ($route->grade);
	$panel  = parse_panel  ($route->panel);
	$setter = parse_setter ($route->setter);
	$notes  = $route->notes;

	$valid = ($colour && $grade && $panel);
	if ($valid) {
		//printf ("valid\n");
	//	try db insert
	//	if (success)
	//		<route result='success'>
	} else {
		//printf ("invalid\n");
	//	<route result='failure'>
	}

	return null;
}

function db_route_add ($route)
{
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
	$xml_result = '<?xml-stylesheet type="text/xsl" href="route.xsl"?'.'>'."\n";
	$xml_result .= "<list type='route'>\n";

	for ($i = 0; $i < $xml->count(); $i++) {
		$message = array();
		$a = $xml->route[$i];
		//$a->colour = "Blue";
		//$a->wibble = "hatstand";
		$route = validate_route ($a, $message);
		if ($route !== null) {
			$route_id = db_route_add ($a);
		} else {
			$route_id = false;
		}

		if ($route_id) {
			$xml_result .= "\t<route result='success'>\n";
			$xml_result .= "\t\t<route_id>$route_id</route_id>\n";
		} else {
			$xml_result .= "\t<route result='failure'>\n";
		}
		$xml_result .= "\t\t<id>$a->id</id>\n";
		$xml_result .= "\t\t<panel>$a->panel</panel>\n";
		$xml_result .= "\t\t<colour>$a->colour</colour>\n";
		$xml_result .= "\t\t<grade>$a->grade</grade>\n";
		$xml_result .= "\t\t<setter>$a->setter</setter>\n";
		$xml_result .= "\t\t<date>$a->date</date>\n";
		$xml_result .= "\t\t<notes>$a->notes</notes>\n";
		foreach ($message as $m) {
			$xml_result .= "\t\t<message>$m</message>\n";
		}
		$xml_result .= "\t</route>\n";
	}

	$xml_result .= "</list>\n";

	echo $xml->asXML();
	return $xml_result;
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
//echo $result;

