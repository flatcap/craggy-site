<?php

set_include_path ("/home/craggy/www");

include "db.php";
include "utils.php";

function colour_main()
{
	$table   = "v_routes";
	$columns = array ("colour");
	$order   = "colour";

	$list = db_select($table, $columns, NULL, $order);

	$totals = array();
	foreach ($list as $row) {
		$c = $row['colour'];
		if (array_key_exists ($c, $totals))
			$totals[$c]++;
		else
			$totals[$c] = 1;
	}

	arsort ($totals);
	$output = "";
	foreach ($totals as $colour => $count) {
		$output .= sprintf ("%s\t%d\n", $colour, $count);
	}

	return $output;
}

echo colour_main();

