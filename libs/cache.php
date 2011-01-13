<?php

include 'db.php';
include 'utils.php';

date_default_timezone_set('UTC');

function cache_uptodate($name)
{
	printf ("Entering: %s\n", __FUNCTION__);

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
		printf ("$name is not in the cache\n");
		return false;
	}
	printf ("$name is in the cache\n");

	// get 'table_XXX' from craggy.data
	$time_db = db_get_data ("table_$name");
	if (!$time_db) {
		printf ("can't find table_$name in craggy.data\n");
		return false;
	}
	printf ("found table_$name in craggy.data\n");

	$time_db = strtotime ($time_db);

	printf ("cache time: %d, %s\n", $time_cache, date ('j M Y H:i:s', $time_cache));
	printf ("db    time: %d, %s\n", $time_db,    date ('j M Y H:i:s', $time_db));

	if ($time_cache > $time_db) {
		printf ("CACHE is newer\n");
	} else {
		printf ("DB is newer\n");
	}

	return ($time_cache > $time_db);
}

function cache_get($name)
{
	printf ("Entering: %s\n", __FUNCTION__);

	static $cache = array();

	if (cache_uptodate ($name)) {
		printf ("cache is uptodate\n");
		if (!array_key_exists ($name, $cache)) {
			printf ("get $name from cache\n");
			$data = apc_fetch ("table_$name");
			$cache[$name] = unserialize ($data);

		}
	} else {
		printf ("cache is not uptodate\n");
		printf ("get $name from db\n");
		$cache[$name] = db_select ($name);
		$data = serialize ($cache[$name]);
		apc_store ("table_$name", $data);
	}

	return $cache[$name];
}


echo "<pre>\n";
//apc_clear_cache ('user');
$name = 'route';

ob_start();

// fill cache
$c = cache_get ($name);

$time_start = microtime (true);
for ($i = 0; $i < 2000; $i++)
	$c = cache_get ($name);
$time_end   = microtime (true);
printf ("$name has %d entries\n", count ($c));

$diff = $time_end - $time_start;

printf ("elapsed = %.6f (1/%.0f) seconds\n", $diff, 1/$diff);

ob_flush();

