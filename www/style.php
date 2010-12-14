<?php

set_include_path ("../libs");

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
			if (array_key_exists ($t, $tag_list)) {
				$tag_list[$t]['count']++;
			} else {
				$tag_list[$t] = array ('style' => $t, 'count' => 1);
			}
		}
	}

	ksort ($tag_list);

	$columns = array ("style", "count");
	$widths = column_widths ($tag_list, $columns, TRUE);
	$widths['style'] *= -1;

	$output .= "<h2>Stats - Styles</h2>";
	$output .= list_render_html ($tag_list, $columns, $widths, "{sortlist: [[0,0]]}");
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

