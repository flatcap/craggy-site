<?php

include "db.php";
include "utils.php";

function stats_age()
{
	$output = "";

	$table   = "v_route";
	$columns = array("id", "date_set");

	$list = db_select($table, $columns);

	$totals = array();
	for ($i = -1; $i < 8; $i++) {
		$totals[$i] = 0;
	}

	$today = strtotime ("today");
	foreach ($list as $row) {
		$date = $row['date_set'];
		if (empty ($date) || ($date == "0000-00-00"))
			$age = -1;
		else
			$age = floor (($today - strtotime ($row['date_set'])) / 2635200);

		if ($age > 7)
			$age = 7;

		$totals[$age]++;
	}

	$output .= "<h2>Stats - Age</h2>";
	$output .= "<img alt='graph of age vs route count' src='img/age.png'>";
	$output .= "<table border='1' cellpadding='3' cellspacing='0'>";
	$output .= "<tr>";
	$output .= "<th>Age</th>";
	$output .= "<th>Count</th>";
	$output .= "<th>Total</th>";
	$output .= "</tr>";

	$total = 0;
	foreach ($totals as $age => $count) {
		if ($age < 0)
			$age = "N/A";
		$total += $count;
		$output .= "<tr>";
		$output .= "<td>$age</td>";
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
	$output .= stats_age();
	$output .= "</div>";
	$output .= get_errors();
	$output .= "</body>";
	$output .= "</html>";

	$header  = html_header ("Age");

	return $header . $output;
}


date_default_timezone_set("UTC");

echo stats_main();

