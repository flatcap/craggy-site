<?php

set_include_path ('../../libs');

include 'db.php';
include 'db_names.php';
include 'utils.php';

$g_colours = null;

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
	if (!$test)
		return null;

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


if (!isset ($_GET))
	return;

if (!array_key_exists ('q', $_GET))
	return;

$q = $_GET['q'];
$len = strlen ($q);
$q = trim ($q);
if (empty ($q))
	return;

$g_colours = db_select($DB_COLOUR);
$lookup = colours_process ($g_colours);

$cid = colours_match ($lookup, $q);
if ($cid !== null) {
	$c = $g_colours[$cid]['colour'];
	printf ('%s,%s', $c, $_GET['id']);
}

