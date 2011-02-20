<?php

set_include_path ('../../libs');

include 'utils.php';

function difficulty_get()
{
	static $difficulty = null;

	if ($difficulty === null) {
		include 'db.php';
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

