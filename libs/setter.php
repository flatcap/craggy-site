<?php

set_include_path ('../../libs');

include 'utils.php';

function setter_get()
{
	static $setter = null;

	if ($setter === null) {
		include 'db.php';
		include 'db_names.php';
		$setter = db_select ($DB_SETTER);
	}

	return $setter;
}

function setter_match ($str)
{
	$setters = setter_get();
	if (!$setters)
		return null;

	$match = null;
	$count = 0;
	foreach ($setters as $key => $s) {
		$name = trim ($s['first_name'] . " " . $s['surname']);
		if (strcasecmp ($str, $s['initials']) === 0) {
			$count++;
			$match = &$setters[$key];
		} else if (partial_match ($str, $s['first_name'])) {
			$count++;
			$match = &$setters[$key];
		} else if (partial_match ($str, $s['surname'])) {
			$count++;
			$match = &$setters[$key];
		} else if (partial_match ($str, $name)) {
			$count++;
			$match = &$setters[$key];
		}
	}

	if ($count == 1) {
		return $match;
	} else {
		return null;
	}
}

function setter_match_xml (&$xml)
{
	$setter = setter_match ($xml->input);
	if ($setter === null) {
		$xml->addChild ('error', "no such setter");
	} else {
		$name = trim ($setter['first_name'] . " " . $setter['surname']);
		$xml->addChild ('setter', $name);
	}
}

