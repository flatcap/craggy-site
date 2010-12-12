<?php

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
			$totals[$c]++;
		else
			$totals[$c] = 1;
	}

	$output .= "<h2>Stats - Colour</h2>";
	$output .= "<img src='img/colour.png'>";
	$output .= "<table border='1' cellpadding='3' cellspacing='0'>";
	$output .= "<tr>";
	$output .= "<th>Colour</th>";
	$output .= "<th>Count</th>";
	$output .= "<th>Total</th>";
	$output .= "</tr>";

	$total = 0;
	foreach ($totals as $colour => $count) {
		$total += $count;
		$output .= "<tr>";
		$output .= "<td>$colour</td>";
		$output .= "<td>$count</td>";
		$output .= "<td>$total</td>";
		$output .= "</tr>";
	}
	$output .= "</table>";

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
