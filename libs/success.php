<?php

set_include_path ('../libs');

include_once 'utils.php';

function success_get($db)
{
	static $success = null;

	if ($success === null) {
		include_once 'db.php';
		include 'db_names.php';
		$success = db_select($db, $DB_SUCCESS);
	}

	return $success;
}

function success_match ($db, $test)
{
	$success = success_get($db);
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

function success_match_xml ($db, &$xml, $test)
{
	$success = success_match ($db, $test);
	if ($success === null) {
		$xml->addChild ('error', sprintf ("'%s' is not a valid success", $test));
		return false;
	} else {
		$xml->success    = $success['outcome'];
		$xml->success_id = $success['id'];
		return true;
	}
}

