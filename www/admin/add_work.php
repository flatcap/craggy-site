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
		return NULL;
}

function colours_match ($lookup, $test)
{
	global $g_colours;

	$test = strtolower ($test);

	$id = colours_match_single ($lookup, $test);
	if ($id !== NULL)
		return $id;

	$pos = strpos ($test, '/');
	if ($pos === FALSE)
		return $id;

	$id1 = colours_match_single ($lookup, substr($test, 0, $pos));
	$id2 = colours_match_single ($lookup, substr($test, $pos+1));

	if (($id1 === NULL) || ($id2 === NULL))
		return NULL;

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
	return $colours[$id]['colour'];
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
		$colour = parse_colour ($colour);
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

	$query = "insert into routes (panel, colour, grade) values ";
	$values = array();

	for ($i = 0; $i < $xml->count(); $i++) {
		$a = $xml->route[$i];
		$values[] = "($a->panel, '$a->colour', '$a->grade')";
	}

	$query .= implode (',', $values);

	return $query;
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


if (isset ($argc)) {
	$_GET = array('action' => 'add', 'data' => '45   red  5+,     gn   6a  ,  bg   6b  ');
}

echo route_main();

