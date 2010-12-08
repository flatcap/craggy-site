<?php

set_include_path ("/home/craggy/www");

include "db.php";
include "utils.php";

function age_main()
{
	$table   = "v_route";
	$columns = array ("id", "date_set");
	$order   = "date_set";

	$list = db_select($table, $columns, NULL, $order);

	process_date ($list, "date_set", TRUE);

	$max_age = 7;
	$totals = array();
	for ($i = 0; $i < $max_age; $i++)
		$totals["$i"] = 0;

	foreach ($list as $row) {
		if (empty ($row['months']))
			continue;
		$m = intval (round ($row['months']));
		if ($m > 6)
			$m = 7;
		if (array_key_exists ($m, $totals))
			$totals[$m]++;
		else
			$totals[$m] = 1;
	}

	$output = "";
	foreach ($totals as $months => $count) {
		$output .= sprintf ("%d\t%d\n", $months, $count);
	}

	return $output;
}

echo age_main();

