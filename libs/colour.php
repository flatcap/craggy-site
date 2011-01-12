<?php

//set_include_path ('../libs');

include 'db.php';
include 'utils.php';

date_default_timezone_set('UTC');

static $colours = null;

/**
 * Cases:
 *	1	$g_colour == null
 *		cache_get ('colour') == null
 *			$g_colour = db_select ('colour');
 *			cache_put ($g_colour);
 *			return ($g_colour);
 *
 *	2	$g_colour == null
 *		cache_get ('colour') == array
 *		cache is up-to-date				// need db txn
 *			$g_colour = cache_get();
 *			return ($g_colour);
 *
 *	3	$g_colour == null
 *		cache_get ('colour') == array
 *		cache is expired				// need db txn
 *			$g_colour = db_select ('colour');
 *			cache_put ($g_colour);
 *			return ($g_colour);
 *
 *	4	$g_colour == array				// => cache is full
 *		cache is up-to-date				// need db txn
 *			return ($g_colour);
 *
 *	5	$g_colour == array
 *		cache is expired				// need db txn
 *			$g_colour = db_select ('colour');
 *			cache_put ($g_colour);
 *			return ($g_colour);
 */

function colour_cache_put ($colours)
{
	printf ("Entering: %s\n", __FUNCTION__);
	$data = serialize ($colours);
	apc_store ('table_colour', $data);
}

function colour_cache_get()
{
	printf ("Entering: %s\n", __FUNCTION__);
	$data = apc_fetch ('table_colour');
	return unserialize ($data);
}


function colour_cache_uptodate()
{
	printf ("Entering: %s\n", __FUNCTION__);

	$time_cache = null;
	$time_db    = null;

	// find 'table_colour' in cache, extract creation_time
	$ci = apc_cache_info('user');
	foreach ($ci['cache_list'] as $item) {
		if ($item['info'] == 'table_colour') {
			$time_cache = $item['creation_time'];
			break;
		}
	}

	if (!$time_cache) {
		return false;
	}

	// get 'table_colour' from craggy.data
	$time_db = db_get_data ('table_colour');
	if (!$time_db) {
		return false;
	}

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

function colour_get()
{
	printf ("Entering: %s\n", __FUNCTION__);

	global $colours;

	if (colour_cache_uptodate()) {
		if (!$colours) {
			$colours = colour_cache_get();
		}
	} else {
		$colours = db_select ('colour');
		colour_cache_put ($colours);
	}

	return $colours;
}


function colour_update_db()
{
	$query = "update colour set colour = 'Beige' where colour = 'Beige'";
	$result = mysql_query($query);
}


function cache_dump()
{
	$ci = apc_cache_info('user');
	printf ("User cache keys:\n");
	foreach ($ci['cache_list'] as $item) {
		printf ("\t%s\n", $item['info']);
	}

	/*
	$ci = apc_cache_info();
	printf ("System cache keys:\n");
	foreach ($ci['cache_list'] as $item) {
		printf ("\t%s\n", $item['filename']);
	}
	*/

}


function test1()
{
	echo "Test 1\n";
	$colours = null;
	apc_clear_cache ('user');
	echo "----------\n";
	$c = colour_get();
	echo "\n--------------------------------------------------------------------------------\n";
}

function test2()
{
	echo "Test 2\n";
	apc_clear_cache ('user');
	$colours = db_select ('colour');
	colour_cache_put ($colours);
	$colours = null;
	echo "----------\n";
	$c = colour_get();
	echo "\n--------------------------------------------------------------------------------\n";
}

function test3()
{
	echo "Test 3\n";
	apc_clear_cache ('user');
	$colours = db_select ('colour');
	colour_cache_put ($colours);
	$colours = null;
	sleep (3);
	colour_update_db();
	echo "----------\n";
	$c = colour_get();
	echo "\n--------------------------------------------------------------------------------\n";
}

function test4()
{
	echo "Test 4\n";
	apc_clear_cache ('user');
	$colours = db_select ('colour');
	colour_cache_put ($colours);
	echo "----------\n";
	$c = colour_get();
	echo "\n--------------------------------------------------------------------------------\n";
}

function test5()
{
	echo "Test 5\n";
	apc_clear_cache ('user');
	$colours = db_select ('colour');
	colour_cache_put ($colours);
	sleep (3);
	colour_update_db();
	echo "----------\n";
	$c = colour_get();
	echo "\n--------------------------------------------------------------------------------\n";
}

function test()
{
	test1();
	sleep (5);
	test2();
	sleep (5);
	test3();
	sleep (5);
	test4();
	sleep (5);
	test5();
}


if (!isset ($argc))
	echo "<pre>";

