<?php

set_include_path ('../../libs');

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

function date_match_xml (&$xml)
{
	$message = "";
	$date = date_match ($xml->input, $message);
	if (empty ($date)) {
		if (empty ($message))
			$message = "Invalid date";
		$xml->addChild ('error', $message);
	} else {
		$xml->addChild ('date', $date);
	}
}


