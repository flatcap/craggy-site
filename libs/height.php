<?php

set_include_path ('../libs');

date_default_timezone_set('UTC');

function height_match (&$str)
{
	$work = strtolower ($str);
	$len  = strlen ($work);

	if ($work[$len-1] == 'm') {
		$work = substr ($work, 0, -1);
	}

	$dot = strpos ($work, '.');
	if ($dot === false) {
		$work .= '.0m';
	} else {
		$work .= 'm';
	}

	if (preg_match ("/^\d+\.\dm$/", $work) > 0) {
		$str = $work;
		return true;
	} else {
		return false;
	}
}

function height_match_xml (&$xml, $test)
{
	if (!height_match ($test)) {
		$xml->addChild ('error', sprintf ("'%s' is not a valid height", $test));
		return false;
	} else {
		$xml->height = $test;
		return true;
	}
}

