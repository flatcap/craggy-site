<?php

include "db.php";
include "utils.php";

function stats_style()
{
	$output = "";

	$table   = "craggy_panel";
	$columns = array("id", "tags");
	$where   = NULL;
	$order   = NULL;

	$list = db_select($table, $columns, $where, $order);

	$tag_list = array();
	foreach ($list as $row) {
		$tags = explode (',', $row['tags']);
		foreach ($tags as $t) {
			if (array_key_exists ($t, $tag_list))
				$tag_list[$t]++;
			else
				$tag_list[$t] = 1;
		}
	}

	$output .= "<h2>Stats - Styles</h2>";
	$output .= "<table id='table_6a' class='tablesorter'>";
	$output .= "<thead>";
	$output .= "<tr>";
	$output .= "<th>Style&nbsp;&nbsp;&nbsp;&nbsp;</th>";
	$output .= "<th>Count&nbsp;&nbsp;&nbsp;&nbsp;</th>";
	$output .= "</tr>";
	$output .= "</thead>";
	$output .= "<tbody>";

	ksort ($tag_list);
	foreach ($tag_list as $tag => $count) {
		$output .= "<tr><td>" . ucfirst($tag) . "</td><td>{$count}</td></tr>";
	}

	$output .= "</tbody>";
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
	$output .= stats_style();
	$output .= "</div>";
	$output .= get_errors();
	$output .= "</body>";
	$output .= "</html>";

	$header  = html_header ("Style");

	return $header . $output;
}


date_default_timezone_set("UTC");

echo stats_main();

