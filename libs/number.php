<?php

set_include_path ('../libs');

date_default_timezone_set('UTC');

function number_match ($str)
{
	return ctype_digit ((string) $str);
}

function number_match_xml (&$xml, $test)
{
	if (!number_match ($test)) {
		$xml->addChild ('error', sprintf ("'%s' is not a valid number", $test));
		return false;
	} else {
		$xml->number = $test;
		return true;
	}
}

