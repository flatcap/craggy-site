<?php

set_include_path ('../libs');

function grade_get($db)
{
	static $grade = null;

	if ($grade === null) {
		include_once 'db.php';
		include 'db_names.php';
		$grade = db_select($db, $DB_GRADE);
	}

	return $grade;
}

function grade_match ($db, $test)
{
	$grade = grade_get($db);
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

function grade_match_xml ($db, &$xml, $test)
{
	$grade = grade_match ($db, $test);
	if ($grade === null) {
		$xml->addChild ('error', sprintf ("'%s' is not a valid grade", $test));
		return false;
	} else {
		$xml->grade    = $grade['grade'];
		$xml->grade_id = $grade['id'];
		return true;
	}
}

