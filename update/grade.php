<?php

date_default_timezone_set('Europe/London');

set_include_path ('../libs');

include 'db.php';
include 'utils.php';

function grade_main()
{
	include 'db_names.php';

	$table   = $DB_V_ROUTE;
	$columns = array ('id', 'grade', 'climb_type');
	$order   = 'grade_seq';

	$db = db_get_database();

	$list = db_select($db, $table, $columns, null, $order);

	$db->close();

	$totals = array(
		'3'   => array (0, 0, 0),
		'3+'  => array (0, 0, 0),
		'4'   => array (0, 0, 0),
		'4+'  => array (0, 0, 0),
		'5'   => array (0, 0, 0),
		'5+'  => array (0, 0, 0),
		'6a'  => array (0, 0, 0),
		'6a+' => array (0, 0, 0),
		'6b'  => array (0, 0, 0),
		'6b+' => array (0, 0, 0),
		'6c'  => array (0, 0, 0),
		'6c+' => array (0, 0, 0),
		'7a'  => array (0, 0, 0),
		'7a+' => array (0, 0, 0),
		'7b'  => array (0, 0, 0),
		'7b+' => array (0, 0, 0),
		'7c'  => array (0, 0, 0),
		'7c+' => array (0, 0, 0),
		'8a'  => array (0, 0, 0),
		'8a+' => array (0, 0, 0));

	foreach ($list as $row) {
		$g = $row['grade'];
		//if (!array_key_exists ($g, $totals))
		//	$totals[$g] = array(0, 0, 0);

		$totals[$g][0]++;
		if ($row['climb_type'] == 'Lead')
			$totals[$g][2]++;
		else
			$totals[$g][1]++;
	}

	$output = '';
	foreach ($totals as $grade => $counts) {
		$output .= sprintf ("%s\t%d\t%d\t%d\n", $grade, $counts[0], $counts[1], $counts[2]);
	}

	return $output;
}

echo grade_main();

