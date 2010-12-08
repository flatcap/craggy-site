<?php

include "db.php";
include "utils.php";

function routes_main($options)
{
	$table   = "v_route";
	$columns = array ("id", "panel", "colour", "grade", "climb_type", "notes", "setter", "date_set");
	$order   = "panel, grade_num, colour";

	$list = db_select($table, $columns, NULL, $order);

	process_date ($list, "date_set", TRUE);
	process_key ($list);

	array_shift ($columns);		// Lose the id column
	$columns[] = "age";
	$columns[] = "months";

	// calculate widths (include headers?)
	$widths = column_widths ($list, $columns, TRUE);

	// alter justification of widths
	fix_justification ($widths);

	$output = "";
	switch ($options["format"]) {
		case "html":
			$last_update = date ("j M Y", strtotime (db_get_last_update()));

			$output .= html_header ("Routes");
			$output .= "<body>";

			$output .= "<div class='download'>";
			$output .= "<h1>Route Data</h1>";
			$output .= "<a href='files/guildford.pdf'><img src='img/pdf.png'></a>";
			$output .= "&nbsp;&nbsp;";
			$output .= "<a href='?format=text'><img src='img/txt.png'></a>";
			$output .= "&nbsp;&nbsp;";
			$output .= "<a href='?format=csv'><img src='img/ss.png'></a>";
			$output .= "</div>";

			$output .= "<div class='header'>All Routes <span>(Last updated: $last_update)</span></div>\n";
			$output .= html_menu();
			$output .= "<div class='content'>\n";
			$output .= list_render_html ($list, $columns, $widths);
			$output .= "</div>";
			$output .= get_errors();
			$output .= "</body>";
			$output .= "</html>";
			break;

		case "csv":
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="routes.csv"');
			$output .= list_render_csv ($list, $columns);
			break;

		case "text":
		default:
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="routes.txt"');
			$output .= list_render_text ($list, $columns, $widths);
			break;
	}

	return $output;
}


date_default_timezone_set("UTC");

$format = array ("csv", "html", "text");

if (isset ($argc)) {
		$longopts = array("format:");

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

echo routes_main ($options);

