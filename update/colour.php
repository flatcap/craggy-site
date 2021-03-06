<?php

date_default_timezone_set('Europe/London');

set_include_path ('../libs');

include 'db.php';
include 'utils.php';

function colour_main()
{
	include 'db_names.php';

	$table   = "$DB_COLOUR,$DB_ROUTE";
	$columns = array ("$DB_COLOUR.id as id", "colour", "count($DB_ROUTE.id) as count");
	$where   = array ("$DB_ROUTE.colour_id = $DB_COLOUR.id", "$DB_ROUTE.date_end is null");
	$order   = 'count desc';
	$group   = 'colour';

	$db = db_get_database();

	$list = db_select($db, $table, $columns, $where, $order, $group);

	$db->close();

	$output = '';
	foreach ($list as $colour) {
		$output .= sprintf ("%s\t%d\n", $colour['colour'], $colour['count']);
	}

	return $output;
}

echo colour_main();

