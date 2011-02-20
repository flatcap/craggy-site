<?php

set_include_path ('../../libs');

include 'success.php';

if (!isset ($_GET))
	return;

if (!array_key_exists ('q', $_GET))
	return;

$q = $_GET['q'];
$len = strlen ($q);
$q = trim ($q);
if (empty ($q))
	return;

$s = success_match ($q);
if ($s !== null) {
	echo $s['outcome'];
}

