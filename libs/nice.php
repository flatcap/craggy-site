<?php

set_include_path ('../../libs');

include 'utils.php';

function nice_match ($str)
{
	if ((partial_match ($str, 'nice')) ||
	    (strcasecmp ($str, 'true') == 0) ||
	    (strcasecmp ($str, 'yes') == 0) ||
	    (strcasecmp ($str, 'y') == 0) ||
	    ($str == '1')) {
		return 'nice';
	}

	if ((strcasecmp ($str, 'no') == 0) ||
	    (strcasecmp ($str, 'false') == 0) ||
	    ($str == '0') ||
	    (empty ($str))) {
		return '';
	}

	return false;
}

function nice_match_xml (&$xml, $test)
{
	$message = "";
	$nice = nice_match ($test, $message);
	if ($nice === false) {
		$xml->addChild ('error', sprintf ("'%s' is not a valid niceness", $test));
		return false;
	} else {
		$xml->addChild ('nice', $nice);
		return true;
	}
}

