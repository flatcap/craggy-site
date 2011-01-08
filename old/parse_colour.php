<?php

set_include_path ('www');

include 'db.php';
include 'utils.php';

$g_routes  = NULL;
$g_panels  = NULL;
$g_colours = NULL;

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

function colours_main()
{
	global $argc;
	global $argv;
	global $g_routes;
	global $g_colours;

	if (!isset ($argc) || ($argc < 2))
		return 0;

	// Format: NUMBER SPACE COLOUR [[,] COLOUR] ...

	array_shift ($argv);
	$text = implode (' ', $argv);
	$colours = preg_split("/[\s,]+/", $text);

	$first = array_shift ($colours);
	if (!is_numeric ($first)) {
		printf ("Not numeric: %s\n", $first);
		return 0;
	}

	$g_routes  = db_select("route");
	$g_colours = db_select("colour");
	$g_panels  = db_select("panel");

	printf ("\n");
	printf ("%d Routes\n",  count ($g_routes));
	printf ("%d Colours\n", count ($g_colours));
	printf ("%d Panels\n",  count ($g_panels));
	printf ("\n");

	$lookup = colours_process ($g_colours);

	$panel = intval ($first);

	$panel_id = NULL;
	foreach ($g_panels as $id => $p) {
		if ($p['number'] == $panel) {
			$panel_id = $p['id'];
			break;
		}
	}

	if ($panel_id === NULL) {
		printf ("Panel '%d' doesn't exist\n", $panel);
		return 0;
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
		printf ("Panel '%d' doesn't have any routes\n", $panel);
		return 0;
	}

	printf ("Found %d routes on panel %d\n", $num_routes, $panel);
	foreach ($matches as $m) {
		printf ("\t%d %s\n", $panel, $g_colours[$m['colour']]['colour']);
	}
	printf ("\n");

	$valid = array();
	$bad   = array();
	foreach ($colours as $c) {
		$id = colours_match ($lookup, $c);
		if ($id === NULL) {
			// Unknown colour
			$bad[] = "Unknown colour: '$c'";
			continue;
		}
		$col = $g_colours[$id]['colour'];
		if (!array_key_exists ($col, $matches)) {
			$bad[] = "Panel $panel doesn't have a $col route";
			continue;
		}
		$valid[] = $matches[$col];
	}

	if (count ($bad) > 0) {
		printf ("Errors:\n");
		foreach ($bad as $b) {
			printf ("\t%s\n", $b);
		}
		return 0;
	}

	printf ("Matched panel %s:\n", $panel);
	foreach ($valid as $v) {
		printf ("\t%s\n", $g_colours[$v['colour']]['colour']);
	}
	printf ("\n");

	return 1;
}


colours_main();

