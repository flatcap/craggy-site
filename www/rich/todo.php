<?php

set_include_path (".:..");

include "db.php";
include "utils.php";

$g_col_sort = array (
	"colour" => "colour",
	"grade" => "grade",
	"panel" => "panel",
	"success" => "success",
	"type" => "type"
);

function todo_command_line ($format, $def_format, $sort)
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

function todo_browser_options ($format, $def_format, $sort)
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

function todo_main($options)
{
	$last_update = date ("j M Y", strtotime (db_get_last_update()));

	$climber_id = 1;
	$table   = "route left join climbs on ((climbs.route_id = route.id) and (climber_id = {$climber_id})) left join colour on (route.colour = colour.id) left join panel on (route.panel = panel.id) left join grade on (route.grade = grade.id) left join v_panel on (route.panel = v_panel.number)";
	$columns = array ("route.id as id", "panel.number as panel", "colour.colour as colour", "grade.grade as grade", "grade.order as grade_num", "climber_id", "date_climbed", "v_panel.climb_type as climb_type", "success", "nice as n", "onsight as o", "difficulty as diff", "climbs.notes as notes");
	$where   = array ("((success <> 'clean') OR (success is NULL))", "grade.order < 600");

	switch ($options["sort"]) {
		case "colour":  $order = "colour, panel, grade";             break;
		case "grade":   $order = "grade_num, panel, colour";         break;
		case "success": $order = "success, panel, grade, colour";    break;
		case "type":    $order = "climb_type, panel, grade, colour"; break;
		default:        $order = "panel, grade_num, colour";         break;
	}

	$list = db_select($table, $columns, $where, $order);

	$columns = array ("panel", "colour", "grade", "climb_type", "success", "notes");

	// calculate widths (include headers?)
	$widths = column_widths ($list, $columns, TRUE);

	// alter justification of widths
	fix_justification ($widths);

	$count  = count ($list);
	$output = "";
	//header("Pragma: no-cache");
	switch ($options["format"]) {
		case "html":
			$last_update = date ("j M Y", strtotime (db_get_last_update()));

			$output .= html_header ("To Do", "../");
			$output .= "<body>";

			$output .= "<div class='download'>";
			$output .= "<h1>Route Data</h1>";
			$output .= "<a href='?format=text'><img src='../img/txt.png'></a>";
			$output .= "&nbsp;&nbsp;";
			$output .= "<a href='?format=csv'><img src='../img/ss.png'></a>";
			$output .= "</div>";

			$output .= "<div class='header'>To Do <span>(Last updated: $last_update)</span></div>\n";
			$output .= html_menu("../");
			$output .= "<div class='content'>\n";
			$output .= "<h2>To Do <span>($count climbs)</span></h2>\n";
			$output .= list_render_html ($list, $columns, $widths);
			$output .= "</div>";
			$output .= get_errors();
			$output .= "</body>";
			$output .= "</html>";
			break;

		case "csv":
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="todo.csv"');
			$output .= list_render_csv ($list, $columns);
			break;

		case "text":
		default:
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="todo.txt"');
			$output .= "To Do ($count climbs)\n";
			$output .= list_render_text ($list, $columns, $widths);
			break;
	}

	return $output;
}


date_default_timezone_set("UTC");

$format = array ("csv", "html", "text");
$sort   = array ("colour", "grade", "panel", "success", "type");

if (isset ($argc))
	$options = todo_command_line ($format, 2, $sort);
else
	$options = todo_browser_options ($format, 1, $sort);

echo todo_main ($options);

