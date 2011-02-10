<?php

set_include_path ('../../libs');

$g_lookup  = null;

function colour_initialise()
{
	include 'db.php';
	include 'db_names.php';

	global $g_lookup;

	$colours = db_select($DB_COLOUR);

	$g_lookup = array();

	foreach ($colours as $ckey => $c) {
		$g_lookup[strtolower ($c['colour'])] = &$colours[$ckey];
		$abbr = explode (',', $c['abbr']);
		foreach ($abbr as $a) {
			$g_lookup[$a] = &$colours[$ckey];
		}
	}
}

function colour_match_single ($test)
{
	global $g_lookup;

	if (!$g_lookup)
		colour_initialise();

	if (!$test)
		return null;

	if (array_key_exists ($test, $g_lookup))
		return $g_lookup[$test];
	else
		return null;
}

function colour_match ($test)
{
	$test = strtolower ($test);

	$id = colour_match_single ($test);
	if ($id !== null)
		return $id;

	$pos = strpos ($test, '/');
	if ($pos === false)
		return $id;

	$col1 = colour_match_single (substr($test, 0, $pos));
	$col2 = colour_match_single (substr($test, $pos+1));

	if (($col1 === null) || ($col2 === null))
		return null;

	$col1 = $col1['colour'];
	$col2 = $col2['colour'];

	$test = strtolower ($col1.'/'.$col2);

	return colour_match_single ($test);
}

