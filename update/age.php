<?php

date_default_timezone_set('Europe/London');

set_include_path ('../libs');

include 'db.php';
include 'utils.php';

function age_main()
{
	include 'db_names.php';

	$output = '';

	$table   = $DB_V_ROUTE;
	$columns = array('id', 'date_set');

	$db = db_get_database();

	$list = db_select($db, $table, $columns);

	$db->close();

	$totals = array();
	for ($i = -1; $i < 8; $i++) {
		$totals[$i] = array ('age' => $i, 'count' => 0);
	}
	$totals[-1]['age'] = 'N/A';

	$today = strtotime ('2018-09-14');	// RAR was 'today'
	foreach ($list as $row) {
		$date = $row['date_set'];
		if (empty ($date) || ($date == '0000-00-00'))
			$age = -1;
		else
			$age = floor (($today - strtotime ($row['date_set'])) / 2635200);

		if ($age > 7)
			$age = 7;

		$totals[$age]['count']++;
	}

	array_shift ($totals);
	foreach  ($totals as $age => $count) {
		$output .= "$age\t{$count['count']}\n";
	}

	return $output;
}

echo age_main();

