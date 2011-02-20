<?php

set_include_path ('../../libs');

include 'utils.php';

function success_get()
{
	static $success = null;

	if ($success === null) {
		include 'db.php';
		include 'db_names.php';
		$success = db_select($DB_SUCCESS);
	}

	return $success;
}

function success_match ($test)
{
	$success = success_get();
	if (!$success)
		return null;

	if (empty ($test))
		return null;

	$count = 0;
	$match = null;
	foreach ($success as $key => $s) {
		if (partial_match ($test, $s['outcome'])) {
			$match = &$success[$key];
			$count++;
		}
	}

	if ($count == 1) {
		return $match;
	} else {
		return null;
	}
}

