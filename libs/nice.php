<?php

set_include_path ('../../libs');

include 'utils.php';

function nice_match ($str, &$message = null)
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

	$message = "Invalid niceness";
	return false;
}

function nice_match_xml (&$xml)
{
	$message = "";
	$nice = nice_match ($xml->input, $message);
	if ($nice === false) {
		if (empty ($message))
			$message = "Invalid niceness";
		$xml->addChild ('error', $message);
	} else {
		$xml->addChild ('nice', $nice);
	}
}
