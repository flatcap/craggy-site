<?php

set_include_path ("www");

include "db.php";
include "utils.php";

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

	$g_colours = db_select("colour");
	$lookup    = colours_process ($g_colours);

	$panel = intval ($first);
	printf ("Panel: %d\n", $panel);

	foreach ($colours as $c) {
		$id = colours_match ($lookup, $c);
		if ($id !== NULL)
			$col = $g_colours[$id]['colour'];
		else
			$col = "Unknown ($c)";
		printf ("\tColour: %s\n", $col);
	}
}


colours_main();

