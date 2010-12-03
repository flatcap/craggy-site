<?php

include "db.php";
include "utils.php";
include "mark.php";

$g_col_sort   = array (
	"age" => "age",
	"colour" => "colour",
	"grade" => "grade",
	"months" => "age",
	"panel" => "panel",
	"set" => "age",
	"setter" => "setter",
	"type" => "type"
);

function routes_command_line ($format, $def_format, $sort)
{
	$longopts  = array("format:", "sort:");

	$options = getopt(NULL, $longopts);

	if (!array_key_exists ("format", $options) || !in_array ($options["format"], $format)) {
		$options["format"] = $format[$def_format];
	}

	if (!array_key_exists ("sort", $options) || !in_array ($options["sort"], $sort)) {
		$options["sort"] = NULL;
	}

	return $options;
}

function routes_browser_options ($format, $def_format, $sort)
{
	$options = array();

	$f = get_url_variable ("format");
	if (!in_array ($f, $format))
		$f = $format[$def_format];

	$s = get_url_variable ("sort");
	if (!in_array ($s, $sort))
		$s = NULL;

	$options["format"] = $f;
	$options["sort"]   = $s;

	return $options;
}

function routes_main($options)
{
	$table   = "v_routes";
	$columns = array ("id", "panel", "colour", "grade", "climb_type", "notes", "setter", "date_set");

	switch ($options["sort"]) {
		case "age":    $order = "date_set desc, panel, grade_num, colour"; $mark = "mark_date_set";   break;
		case "colour": $order = "colour, panel, grade";                    $mark = "mark_colour";     break;
		case "grade":  $order = "grade_num, panel, colour";                $mark = "mark_grade";      break;
		case "setter": $order = "setter, panel, grade, colour";            $mark = "mark_setter";     break;
		case "type":   $order = "climb_type, panel, grade, colour";        $mark = "mark_climb_type"; break;
		default:       $order = "panel, grade_num, colour";                $mark = "mark_panel";      break;
	}

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
			$output .= list_render_html ($list, $columns, $widths, $mark);
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
$sort   = array ("age", "colour", "grade", "panel", "setter", "type");

if (isset ($argc))
	$options = routes_command_line ($format, 2, $sort);
else
	$options = routes_browser_options ($format, 1, $sort);

echo routes_main ($options);

