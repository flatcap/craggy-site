<?php

set_include_path ('../libs');

include_once 'utils.php';

function difficulty_get($db)
{
	static $difficulty = null;

	if ($difficulty === null) {
		include_once 'db.php';
		include 'db_names.php';
		$difficulty = db_select($db, $DB_DIFFICULTY);
	}

	return $difficulty;
}

function difficulty_match ($db, $test)
{
	$difficulty = difficulty_get($db);
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

function difficulty_match_xml ($db, &$xml, $test)
{
	$difficulty = difficulty_match ($db, $test);
	if ($difficulty === null) {
		$xml->addChild ('error', sprintf ("'%s' is not a valid difficulty", $test));
		return false;
	} else {
		$xml->difficulty    = $difficulty['description'];
		$xml->difficulty_id = $difficulty['id'];
		return true;
	}
}

