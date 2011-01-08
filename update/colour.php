<?php

set_include_path ('../libs');

include 'db.php';
include 'utils.php';

function colour_main()
{
	include 'db_names.php';

	$table   = "$DB_COLOUR,$DB_ROUTE";
	$columns = array ("$DB_COLOUR.id as id", "colour", "count($DB_ROUTE.id) as count");
	$where   = "$DB_ROUTE.colour_id = $DB_COLOUR.id";
	$order   = 'count desc';
	$group   = 'colour';

	$list = db_select($table, $columns, $where, $order, $group);

	$output = '';
	foreach ($list as $colour) {
		$output .= sprintf ("%s\t%d\n", $colour['colour'], $colour['count']);
	}

	return $output;
}

echo colour_main();

