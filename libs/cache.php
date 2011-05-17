<?php

include_once 'db.php';

function cache_uptodate ($name)
{
	$time_cache = null;
	$time_db    = null;

	// find 'table_XXX' in cache, extract creation_time
	$ci = apc_cache_info ('user');
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

function cache_get_table ($name)
{
	static $cache = array();

	if (cache_uptodate ($name)) {
		if (!array_key_exists ($name, $cache)) {
			$data = apc_fetch ("table_$name");
			$cache[$name] = unserialize ($data);
			// if unserialize fails, I don't want to put null into my local cache

		}
	} else {
		$cache[$name] = db_select ($name);
		// if db_select fails I don't want to put null into my local cache
		$data = serialize ($cache[$name]);
		apc_store ("table_$name", $data);
	}

	return $cache[$name];
}


function cache_put_something_else()
{
	// how do I tie the life time of this object to a particular db table?
}

function cache_get_something_else()
{
}

