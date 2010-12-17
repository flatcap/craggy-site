<?php

set_include_path ("../libs");

include "db.php";
include "utils.php";

function colour_main()
{
	$table   = "craggy_colour, craggy_route";
	$columns = array ("craggy_colour.id as id", "colour", "count(craggy_route.id) as count");
	$where   = "craggy_route.colour_id = craggy_colour.id";
	$order   = "count desc";
	$group   = "colour";

	$list = db_select($table, $columns, $where, $order, $group);

	$output = "";
	foreach ($list as $colour) {
		$output .= sprintf ("%s\t%d\n", $colour['colour'], $colour['count']);
	}

	return $output;
}

echo colour_main();

