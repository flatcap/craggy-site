<?php

set_include_path ('../../libs');

include 'db.php';
include 'utils.php';

date_default_timezone_set('UTC');

$cache_lifetime = 20; // seconds

function buffer_cache_expiry()
{
	$expiry = apc_fetch ('6a_expiry');
	if (empty ($expiry) || !is_numeric ($expiry))
		return null;

	$age = $expiry - microtime (true);
	if ($age < 0)
		return null;

	return sprintf ('%.1f', $age);
}

function buffer_cache_clear()
{
	//apc_delete ('6a_list');
	//apc_delete ('6a_expiry');

	apc_clear_cache ('user');
}

function buffer_cache_keep()
{
	global $cache_lifetime;

	$expiry = microtime (true) + $cache_lifetime;
	apc_store ('6a_expiry', $expiry);
}

function buffer_cache_put ($list)
{
	buffer_cache_clear();
	buffer_cache_keep();

	$data = serialize ($list);
	apc_store ('6a_list',   $data);
}

function buffer_cache_get()
{
	$data = apc_fetch ('6a_list');
	if (empty ($data))
		return null;

	$expiry = buffer_cache_expiry();
	if ($expiry === null)
		return null;

	return unserialize ($data);
}


function buffer_db_get()
{
	include 'db_names.php';

	$table   = $DB_V_ROUTE;
	$columns = array ('id', 'panel', 'colour', 'grade');
	$where   = array ('grade_seq >= 400', 'grade_seq < 500', "climb_type <> 'lead'");
	$where   = null;
	$order   = 'panel_seq, grade_seq, colour';
	//$order  .= ' limit 10';

	$list = db_select($table, $columns, $where, $order);

	buffer_cache_put ($list);
	return $list;
}

function buffer_render_xml ($name, $list)
{
	$output = '';
	foreach ($list as $row) {
		$output .= "\t<$name>\n";
		$output .= "\t\t<id>{$row['id']}</id>\n";
		$output .= "\t\t<panel>{$row['panel']}</panel>\n";
		$output .= "\t\t<colour>{$row['colour']}</colour>\n";
		$output .= "\t\t<grade>{$row['grade']}</grade>\n";
		$output .= "\t</$name>\n";
	}

	return $output;
}


function buffer_get_6a (&$notes)
{
	$list = buffer_cache_get();
	if ($list === null) {
		$list = buffer_db_get();
		$notes = 'data from database';
	} else {
		$notes = 'data from cache';
	}

	return $list;
}

function buffer_read()
{
	$notes = '';

	$time_start = microtime (true);
	$list = buffer_get_6a ($notes);
	$time_end   = microtime (true);
	$diff = $time_end - $time_start;
	if ($diff == 0)
		$diff = 1;

	$time_elapsed = sprintf ('%.6f (1/%.0f)', $diff, 1/$diff);

	$xml = buffer_render_xml ('route', $list);

	$expiry = buffer_cache_expiry();

	$xml = "<data retrieve_time='$time_elapsed' cache_expiry='$expiry' notes='$notes'>\n" . $xml . "</data>\n";
	return ($xml);
}


function buffer_main()
{
	global $_GET;

	$output = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?" . ">\n";

	if (isset ($_GET) && array_key_exists ('action', $_GET))
		$action = $_GET['action'];
	else
		$action = '';

	switch ($action) {
		case 'read':
			$output .= buffer_read();
			break;
		case 'clear':
			buffer_cache_clear();
			$output .= "<data notes='cache cleared'></data>";
			break;
		case 'keep':
			buffer_cache_keep();
			$expiry = buffer_cache_expiry();
			$notes = 'cache kept';
			$output .= "<data cache_expiry='$expiry' notes='$notes'></data>\n";
			break;
		default:
			$output .= "<data notes='unknown action'></data>\n";
			break;
	}

	return $output;
}


header('Pragma: no-cache');
header('Content-Type: application/xml; charset=ISO-8859-1');

echo buffer_main();

