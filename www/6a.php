<?php

set_include_path ("../libs");

include "db.php";
include "utils.php";

function six_main ($options)
{
	// "difficulty"
	$table   = "v_route";
	$columns = array ("id", "panel", "colour", "grade", "height");
	$where   = array ("grade_seq >= 400", "grade_seq < 500", "climb_type <> 'lead'");
	$order   = "panel_seq, grade_seq, colour";

	$list = db_select($table, $columns, $where, $order);

	$total_height = process_height_total ($list);
	process_height_abbreviate ($list);

	array_shift ($columns);		// Ignore the id column
	$widths = column_widths ($list, $columns, TRUE);
	fix_justification ($widths);

	$count  = count ($list);
	$output = "";
	switch ($options["format"]) {
		case "html":
			$last_update = date ("j M Y", strtotime (db_get_last_update()));

			$output .= html_header ("6a");
			$output .= "<body>";

			$output .= "<div class='download'>";
			$output .= "<h1>Route Data</h1>";
			//$output .= "<a href='files/guildford_6a.pdf'><img alt='6a route list as a pdf document' width='24' height='24' src='img/pdf.png'></a>";
			//$output .= "&nbsp;&nbsp;";
			$output .= "<a href='?format=text'><img alt='6a route list as a formatted text document' width='24' height='24' src='img/txt.png'></a>";
			$output .= "&nbsp;&nbsp;";
			$output .= "<a href='?format=csv'><img alt='6a route list as a csv document' width='24' height='24' src='img/ss.png'></a>";
			$output .= "</div>";

			$output .= "<div class='header'>Top Roped 6a Routes <span>(Last updated: $last_update)</span></div>\n";
			$output .= html_menu();
			$output .= "<div class='content'>\n";
			$output .= list_render_html ($list, $columns, $widths, "{sortlist: [[0,0],[2,0],[1,0]]}");
			$output .= "<p>$count climbs ({$total_height}m)</p>";
			$output .= "</div>";
			$output .= get_errors();
			$output .= "</body>";
			$output .= "</html>";
			break;

		case "csv":
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="guildford_6a.csv"');
			$output .= list_render_csv ($list, $columns);
			break;

		case "text":
		default:
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="guildford_6a.txt"');
			$output .= list_render_text ($list, $columns, $widths);
			$output .= "\r\n$count climbs ({$total_height}m)\r\n";
			break;
	}

	return $output;
}


date_default_timezone_set("UTC");

$format = array ("csv", "html", "text");

if (isset ($argc)) {
	$longopts  = array("format:");

	$options = getopt(NULL, $longopts);

	if (!array_key_exists ("format", $options) || !in_array ($options["format"], $format)) {
		$options["format"] = $format[2];
	}
} else {
	$options = array();

	$f = get_url_variable ("format");
	if (!in_array ($f, $format))
		$f = $format[1];

	$options["format"] = $f;
}

echo six_main ($options);

