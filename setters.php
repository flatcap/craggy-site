<?php

set_include_path ('libs');

include_once 'db.php';
include_once 'utils.php';

function stats_setters()
{
	include 'db_names.php';

	$output = '';

	$table   = $DB_V_ROUTE;
	$columns = array('id', 'setter');
	$where   = null;
	$order   = 'setter';

	$list = db_select($table, $columns, $where, $order);

	$setters = array();
	foreach ($list as $s) {
		$name = $s['setter'];
		if (empty ($name)) {
			$name = 'N/A';
		}
		if (array_key_exists ($name, $setters)) {
			$setters[$name]['count']++;
		} else {
			$setters[$name] = array ('setter' => $name, 'count' => 1);
		}
	}

	$output .= '<h2>Stats - Setters</h2>';
	$columns = array ('setter', 'count');
	$widths = column_widths ($setters, $columns, true);
	fix_justification ($widths);

	$output .= list_render_html ($setters, $columns, $widths, '{sortlist: [[1,1],[0,0]]}');

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

	$output .= stats_setters();
	$output .= '</div>';
	$output .= get_errors();
	$output .= '</body>';
	$output .= '</html>';

	$header  = html_header ('Setter');

	return $header . $output;
}


date_default_timezone_set('UTC');

echo stats_main();
