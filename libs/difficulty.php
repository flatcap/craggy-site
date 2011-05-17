<?php

set_include_path ('../../libs');

include_once 'utils.php';

function difficulty_get()
{
	static $difficulty = null;

	if ($difficulty === null) {
		include_once 'db.php';
		include 'db_names.php';
		$difficulty = db_select($DB_DIFFICULTY);
	}

	return $difficulty;
}

function difficulty_match ($test)
{
	$difficulty = difficulty_get();
	if (!$difficulty)
		return null;

	if (empty ($test))
		return null;

	$count = 0;
	$match = null;
	foreach ($difficulty as $key => $s) {
		if (strcasecmp ($test, $s['abbr']) == 0) {
			$match = &$difficulty[$key];
			$count++;
			break;
		}
		if (partial_match ($test, $s['description'])) {
			$match = &$difficulty[$key];
			$count++;
		}
	}

	if ($count == 1) {
		return $match;
	} else {
		return null;
	}
}

function difficulty_match_xml (&$xml, $test)
{
	$difficulty = difficulty_match ($test);
	if ($difficulty === null) {
		$xml->addChild ('error', sprintf ("'%s' is not a valid difficulty", $test));
		return false;
	} else {
		$xml->difficulty    = $difficulty['description'];
		$xml->difficulty_id = $difficulty['id'];
		return true;
	}
}

