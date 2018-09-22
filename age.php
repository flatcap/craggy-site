<?php

set_include_path ('libs');

include_once 'db.php';
include_once 'utils.php';

function stats_age($db)
{
	include 'db_names.php';

	$output = '';

	$table   = $DB_V_ROUTE;
	$columns = array('id', 'date_set');

	$list = db_select($db, $table, $columns);

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

	$output .= '<h2>Stats - Age</h2>';
	$output .= "<img alt='graph of age vs route count' width='800' height='400' src='style/age.png'>";

	$columns = array ('age', 'count');
	$widths = column_widths ($totals, $columns, true);

	$output .= list_render_html ($totals, $columns, $widths, '{sortlist: [[0,0]]}');
	return $output;
}

function stats_main()
{
	$db = db_get_database();

	$type = get_url_variable('type');

	$last_update = date ('j M Y', strtotime (db_get_last_update($db)));

	$output  = '<body>';

	$output .= html_menu();

	$output .= "<div class='content'>";
	$output .= "<div class='title'>";
	$output .= "<h1>Craggy Routes</h1>";
	$output .= '</div>';

	$output .= stats_age($db);
	$output .= '</div>';
	$output .= get_errors();
	$output .= html_footer();

	$header  = html_header ('Age');

	return $header . $output;
}


date_default_timezone_set('Europe/London');

echo stats_main();

