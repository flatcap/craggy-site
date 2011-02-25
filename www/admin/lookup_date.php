<?php

date_default_timezone_set('UTC');

if (!isset ($_GET))
	return;
if (!array_key_exists ('q', $_GET))
	return;

$q = trim ($_GET['q']);
if (empty ($q))
	return;

$date = strtotime ($q);
$now  = strtotime ('now');

if ($date == false)
	$result = 'Invalid date';
else if ($date > $now)
	$result = 'Date cannot be in the future';
else
	$result = strftime('%Y-%m-%d', $date);

//printf ('%s (%s)', $q, $result);
echo $result;

