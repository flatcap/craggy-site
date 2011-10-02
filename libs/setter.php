<?php

set_include_path ('../libs');

include_once 'utils.php';

function setter_get()
{
	static $setter = null;

	if ($setter === null) {
		include_once 'db.php';
		include 'db_names.php';
		$columns = array ('id', 'initials', 'first_name', 'surname', 'trim(concat(first_name, " ", surname)) as name');
		$setter = db_select ($DB_SETTER, $columns);
	}

	return $setter;
}

function setter_match ($test)
{
	$setter = setter_get();
	if (!$setter)
		return null;

	$count = 0;
	$match = null;
	foreach ($setter as $key => $s) {
		$name = trim ($s['first_name'] . " " . $s['surname']);
		if (strcasecmp ($test, $s['initials']) === 0) {
			$count++;
			$match = &$setter[$key];
		} else if (partial_match ($test, $s['first_name'])) {
			$count++;
			$match = &$setter[$key];
		} else if (partial_match ($test, $s['surname'])) {
			$count++;
			$match = &$setter[$key];
		} else if (partial_match ($test, $name)) {
			$count++;
			$match = &$setter[$key];
		}
	}

	if ($count == 1) {
		return $match;
	} else {
		return null;
	}
}

function setter_match_xml (&$xml, $test)
{
	$setter = setter_match ($test);
	if ($setter === null) {
		$xml->addChild ('error', sprintf ("'%s' is not a valid setter", $test));
		return false;
	} else {
		$name = trim ($setter['first_name'] . " " . $setter['surname']);
		$xml->setter    = $name;
		$xml->setter_id = $setter['id'];
		return true;
	}
}

