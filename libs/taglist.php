<?php

set_include_path ('../libs');

date_default_timezone_set('UTC');

function taglist_match (&$str)
{
	$tags = explode (',', $str);

	foreach ($tags as $index => $t) {
		$tags[$index] = trim ($t);
	}

	$str = implode (',', $tags);

	return true;
}

function taglist_match_xml (&$xml, $test)
{
	taglist_match ($test);
	$xml->taglist = $test;
	return true;
}


