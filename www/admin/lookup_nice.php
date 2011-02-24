<?php

set_include_path ('../../libs');

include 'utils.php';

if (!isset ($_GET))
	return;

if (!array_key_exists ('q', $_GET))
	return;

$q = $_GET['q'];
$len = strlen ($q);
$q = trim ($q);
if (empty ($q))
	return;

if ((partial_match ($q, 'nice')) ||
    (strcasecmp ($q, 'true') == 0) ||
    (strcasecmp ($q, 'yes') == 0) ||
    (strcasecmp ($q, 'y') == 0) ||
    ($q == '1')) {
	echo 'nice';
}

