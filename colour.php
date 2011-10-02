<?php

set_include_path ('libs');

include_once 'db.php';
include_once 'utils.php';

function stats_colour()
{
	include 'db_names.php';

	$output = '';

	$table   = "$DB_COLOUR,$DB_ROUTE";
	$columns = array ("$DB_COLOUR.id as id", "colour", "count($DB_ROUTE.id) as count");
	$where   = "$DB_ROUTE.colour_id = $DB_COLOUR.id";
	$order   = 'count desc';
	$group   = 'colour';

	$list = db_select($table, $columns, $where, $order, $group);

	$output .= '<h2>Stats - Colour</h2>';
	$output .= "<img alt='graph of colour vs frequency' width='800' height='400' src='img/colour.png'>";

	$columns = array ('colour', 'count');
	$widths = column_widths ($list, $columns, true);
	$widths['colour'] *= -1;
	$output .= list_render_html ($list, $columns, $widths, '{sortlist: [[1,1],[0,0]]}');

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

	$output .= stats_colour();
	$output .= '</div>';
	$output .= get_errors();
	$output .= '</body>';
	$output .= '</html>';

	$header  = html_header ('Colour');

	return $header . $output;
}


date_default_timezone_set('UTC');

echo stats_main();

