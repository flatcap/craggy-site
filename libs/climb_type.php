<?php

set_include_path ('../../libs');

include_once 'utils.php';

function climb_type_get()
{
	static $climb_type = null;

	if ($climb_type === null) {
		include 'db.php';
		include 'db_names.php';
		$climb_type = db_select ($DB_CLIMB_TYPE);
	}

	return $climb_type;
}

function climb_type_match ($test)
{
	$climb_type = climb_type_get();
	if (!$climb_type)
		return null;

	if (empty ($test))
		return null;

	$count = 0;
	$match = null;
	foreach ($climb_type as $key => $c) {
		if (strcasecmp ($test, $c['abbr']) == 0) {
			$match = &$climb_type[$key];
			$count++;
			break;
		}
		if (partial_match ($test, $c['climb_type'])) {
			$match = &$climb_type[$key];
			$count++;
		}
	}

	if ($count == 1) {
		return $match;
	} else {
		return null;
	}
}

function climb_type_match_xml (&$xml, $test)
{
	$climb_type = climb_type_match ($test);
	if ($climb_type === null) {
		$xml->addChild ('error', sprintf ("'%s' is not a valid climb type", $test));
		return false;
	} else {
		$xml->climb_type    = $climb_type['climb_type'];
		$xml->climb_type_id = $climb_type['id'];
		return true;
	}
}

