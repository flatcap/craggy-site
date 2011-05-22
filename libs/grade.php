<?php

set_include_path ('../../libs');

function grade_get()
{
	static $grade = null;

	if ($grade === null) {
		include_once 'db.php';
		include 'db_names.php';
		$grade = db_select($DB_GRADE);
	}

	return $grade;
}

function grade_match ($test)
{
	$grade = grade_get();
	if (!$grade)
		return null;

	if (empty ($test))
		return null;

	foreach ($grade as $key => $g) {
		if (strcasecmp ($test, $g['grade']) == 0) {
			return $grade[$key];
		}
	}

	return null;
}

function grade_match_xml (&$xml, $test)
{
	$grade = grade_match ($test);
	if ($grade === null) {
		$xml->addChild ('error', sprintf ("'%s' is not a valid grade", $test));
		return false;
	} else {
		$xml->grade    = $grade['grade'];
		$xml->grade_id = $grade['id'];
		return true;
	}
}

