<?php

set_include_path ('../../libs');

include 'difficulty.php';

if (!isset ($_GET))
	return;

if (!array_key_exists ('q', $_GET))
	return;

$q = $_GET['q'];
$len = strlen ($q);
$q = trim ($q);
if (empty ($q))
	return;

$s = difficulty_match ($q);
if ($s !== null) {
	echo $s['description'];
}

