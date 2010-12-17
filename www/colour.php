<?php

set_include_path ("../libs");

include "db.php";
include "utils.php";

function stats_colour()
{
	$output = "";

	$table   = "craggy_colour, craggy_route";
	$columns = array ("craggy_colour.id as id", "colour", "count(craggy_route.id) as count");
	$where   = "craggy_route.colour_id = craggy_colour.id";
	$order   = "count desc";
	$group   = "colour";

	$list = db_select($table, $columns, $where, $order, $group);

	$output .= "<h2>Stats - Colour</h2>";
	$output .= "<img alt='graph of colour vs frequency' width='800' height='400' src='img/colour.png'>";

	$columns = array ('colour', 'count');
	$widths = column_widths ($list, $columns, TRUE);
	$widths['colour'] *= -1;
	$output .= list_render_html ($list, $columns, $widths, "{sortlist: [[1,1],[0,0]]}");

	return $output;
}

function stats_main()
{
	$type = get_url_variable('type');

	$last_update = date ("j M Y", strtotime (db_get_last_update()));

	$output  = "<body>";
	$output .= "<div class='header'>Craggy Routes <span>(Last updated: $last_update)</span></div>\n";
	$output .= html_menu();
	$output .= "<div class='content'>\n";
	$output .= stats_colour();
	$output .= "</div>";
	$output .= get_errors();
	$output .= "</body>";
	$output .= "</html>";

	$header  = html_header ("Colour");

	return $header . $output;
}


date_default_timezone_set("UTC");

echo stats_main();

