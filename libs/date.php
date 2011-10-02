<?php

set_include_path ('../libs');

date_default_timezone_set('UTC');

function date_match ($str, &$message = null)
{
	$date = strtotime ($str);
	$now  = strtotime ('now');

	$result = "";
	if ($date == false)
		$message = 'Invalid date';
	else if ($date > $now) {
		$message = 'Date cannot be in the future';
	} else {
		$result = strftime('%Y-%m-%d', $date);
		$message = null;
	}

	return $result;
}

function date_match_xml (&$xml, $test)
{
	$message = "";
	$date = date_match ($test, $message);
	if (empty ($date)) {
		$xml->addChild ('error', $message);
		return false;
	} else {
		$xml->date = $date;
		return true;
	}
}


