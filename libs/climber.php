<?php

set_include_path ('../../libs');

include_once 'utils.php';

function climber_get()
{
	static $climber = null;

	if ($climber === null) {
		include_once 'db.php';
		include 'db_names.php';
		$columns = array ('id', 'first_name', 'surname', 'trim(concat(first_name, " ", surname)) as name');
		$climber = db_select ($DB_CLIMBER, $columns);
	}

	return $climber;
}

function climber_match ($str)
{
	$climbers = climber_get();
	if (!$climbers)
		return null;

	$match = null;
	$count = 0;
	foreach ($climbers as $key => $s) {
		$name = trim ($s['first_name'] . " " . $s['surname']);
		if (partial_match ($str, $s['first_name'])) {
			$count++;
			$match = &$climbers[$key];
		} else if (partial_match ($str, $s['surname'])) {
			$count++;
			$match = &$climbers[$key];
		} else if (partial_match ($str, $name)) {
			$count++;
			$match = &$climbers[$key];
		}
	}

	if ($count == 1) {
		return $match;
	} else {
		return null;
	}
}

function climber_match_xml (&$xml, $test)
{
	$climber = climber_match ($test);
	if ($climber === null) {
		$xml->addChild ('error', sprintf ("'%s' is not a valid climber", $test));
		return false;
	} else {
		$name = trim ($climber['first_name'] . " " . $climber['surname']);
		$xml->climber    = $name;
		$xml->climber_id = $climber['id'];
		return true;
	}
}

