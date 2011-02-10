<?php

set_include_path ('../../libs');

include 'colour.php';

if (!isset ($_GET))
	return;

if (!array_key_exists ('q', $_GET))
	return;

$q = $_GET['q'];
$len = strlen ($q);
$q = trim ($q);
if (empty ($q))
	return;

$col = colour_match ($q);
if ($col !== null) {
	printf ('%s,%s', $col['colour'], $_GET['id']);
}

