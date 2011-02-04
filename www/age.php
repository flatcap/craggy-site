<?php

set_include_path ('../libs');

include 'db.php';
include 'utils.php';

function stats_age()
{
	include 'db_names.php';

	$output = '';

	$table   = $DB_V_ROUTE;
	$columns = array('id', 'date_set');

	$list = db_select($table, $columns);

	$totals = array();
	for ($i = -1; $i < 8; $i++) {
		$totals[$i] = array ('age' => $i, 'count' => 0);
	}
	$totals[-1]['age'] = 'N/A';

	$today = strtotime ('today');
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
	$output .= "<img alt='graph of age vs route count' width='800' height='400' src='img/age.png'>";

	$columns = array ('age', 'count');
	$widths = column_widths ($totals, $columns, true);

	$output .= list_render_html ($totals, $columns, $widths, '{sortlist: [[0,0]]}');
	return $output;
}

function stats_main()
{
	$type = get_url_variable('type');

	$last_update = date ('j M Y', strtotime (db_get_last_update()));

	$output  = '<body>';

	$output .= html_menu();

	$output .= "<div class='content'>";
	$output .= "<div class='title'>";
	$output .= "<h1>Craggy Routes</h1> <span>(Last updated: $last_update)</span>";
	$output .= '</div>';

	$output .= stats_age();
	$output .= '</div>';
	$output .= get_errors();
	$output .= '</body>';
	$output .= '</html>';

	$header  = html_header ('Age');

	return $header . $output;
}


date_default_timezone_set('UTC');

echo stats_main();

