<?php

set_include_path ('../../libs');

include 'setter2.php';

if (!isset ($_GET))
	return;

if (!array_key_exists ('q', $_GET))
	return;

$q = $_GET['q'];
$len = strlen ($q);
$q = trim ($q);
if (empty ($q))
	return;

$q = preg_replace ("/\s+/", " ", $q);
$s = setter_match ($q);
if ($s !== null) {
	echo trim ($s['first_name'] . " " . $s['surname']);
}

