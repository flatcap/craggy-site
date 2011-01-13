<?php

include 'db.php';
include 'utils.php';

date_default_timezone_set('UTC');

function cache_uptodate($name)
{
	$time_cache = null;
	$time_db    = null;

	// find 'table_XXX' in cache, extract creation_time
	$ci = apc_cache_info('user');
	foreach ($ci['cache_list'] as $item) {
		if ($item['info'] == "table_$name") {
			$time_cache = $item['creation_time'];
			break;
		}
	}

	if (!$time_cache) {
		return false;
	}

	// get 'table_XXX' from craggy.data
	$time_db = db_get_data ("table_$name");
	if (!$time_db) {
		return false;
	}

	$time_db = strtotime ($time_db);

	return ($time_cache > $time_db);
}

function cache_get($name)
{
	static $colours = null;

	if (cache_uptodate()) {
		if (!$colours) {
			$data = apc_fetch ("table_$name");
			$colours = unserialize ($data);

		}
	} else {
		$colours = db_select ($name);
		$data = serialize ($colours);
		apc_store ("table_$name", $data);
	}

	return $colours;
}

