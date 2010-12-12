<?php

include "db.php";
include "utils.php";

function stats_setters()
{
	$output = "";

	$table   = "v_route";
	$columns = array("id", "setter");
	$where   = NULL;
	$order   = "setter";

	$list = db_select($table, $columns, $where, $order);

	$setters = array();
	foreach ($list as $s) {
		$name = $s['setter'];
		if (empty ($name))
			$name = "N/A";
		if (array_key_exists ($name, $setters))
			$setters[$name]++;
		else
			$setters[$name] = 1;
	}

	$output .= "<h2>Stats - Setters</h2>";
	$output .= "<table border='1' cellpadding='3' cellspacing='0'>";
	$output .= "<tr>";
	$output .= "<th>Setter</th>";
	$output .= "<th>Count</th>";
	$output .= "</tr>";

	foreach ($setters as $name => $count) {
		$output .= "<tr><td>$name</td><td>$count</td></tr>";
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
	$output .= stats_setters();
	$output .= "</div>";
	$output .= get_errors();
	$output .= "</body>";
	$output .= "</html>";

	$header  = html_header ("Setter");

	return $header . $output;
}


date_default_timezone_set("UTC");

echo stats_main();

