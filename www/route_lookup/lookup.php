<?php

set_include_path (".:..");

include "db.php";
include "utils.php";

$g_routes  = NULL;
$g_panels  = NULL;
$g_colours = NULL;

if (!isset ($_GET))
	return;
	
if (!array_key_exists ('q', $_GET))
	return;

$q=$_GET["q"];
$len = strlen ($q);
$trail_space = ($q[$len-1] == " ");
$q=trim ($q);
if (empty ($q))
	return;

$parts = preg_split("/[\s,]+/", $q);
$panel = array_shift ($parts);

if (!is_numeric ($panel)) {
	printf ("Not a panel number: '%s'", $panel);
	return;
}

$g_routes  = db_select("route");
$g_colours = db_select("colour");
$g_panels  = db_select("panel");

$panel_id = NULL;
foreach ($g_panels as $id => $p) {
	if ($p['number'] == $panel) {
		$panel_id = $p['id'];
		break;
	}
}

if ($panel_id === NULL) {
	printf ("Panel '%d' doesn't exist\n", $panel);
	return;
}

$matches = array();
foreach ($g_routes as $id => $r) {
	if ($r['panel'] == $panel_id) {
		$col_id = $r['colour'];
		$col = $g_colours[$r['colour']]['colour'];
		$matches[$col] = $r;
	}
}

$num_routes = count ($matches);
if ($num_routes == 0) {
	printf ("Panel '%d' doesn't have any routes", $panel);
	return;
}

$list = array();
foreach ($matches as $m) {
	$list[] = $g_colours[$m['colour']]['colour'];
}

function colours_process ($colours)
{
	$lookup = array();

	foreach ($colours as $ckey => $c) {
		$lookup[strtolower ($c['colour'])] = &$colours[$ckey];
		$abbr = explode (',', $c['abbr']);
		foreach ($abbr as $a) {
			$lookup[$a] = &$colours[$ckey];
		}
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


$lookup = colours_process ($g_colours);

foreach ($parts as &$p) {
	$id = colours_match ($lookup, $p);
	if ($id !== NULL) {
		$p = $g_colours[$id]['colour'];
	}
}

$c = $panel;
$cols = implode (", ", $parts);
if (!empty ($cols))
	$c .= " " . $cols;

if ($trail_space)
	$c .= " ";
printf ("%s - %d %s", $c, $panel, implode (", ", $list));

