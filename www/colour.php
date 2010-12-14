<?php

set_include_path ("../libs");

include "db.php";
include "utils.php";

function stats_colour()
{
	$output = "";

	$table   = "v_route";
	$columns = array("id", "colour");
	$order   = "colour";

	$list = db_select($table, $columns, NULL, $order);

	$totals = array();
	foreach ($list as $row) {
		$c = $row['colour'];
		if (array_key_exists ($c, $totals))
			$totals[$c]['count']++;
		else
			$totals[$c] = array ('colour' => $c, 'count' => 1);
	}

	$output .= "<h2>Stats - Colour</h2>";
	$output .= "<img alt='graph of colour vs frequency' src='img/colour.png'>";

	$columns = array ('colour', 'count');
	$widths = column_widths ($totals, $columns, TRUE);
	$widths['colour'] *= -1;
	$output .= list_render_html ($totals, $columns, $widths, "{sortlist: [[1,1],[0,0]]}");

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

