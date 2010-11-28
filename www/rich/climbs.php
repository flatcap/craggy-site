<?php

set_include_path (".:..");

include "db.php";
include "utils.php";
include "mark.php";

$g_col_sort   = array (
	"age" => "age",
	"climbed" => "age",
	"colour" => "colour",
	"grade" => "grade",
	"months" => "age",
	"panel" => "panel",
	"success" => "success",
	"type" => "type",
	"d" => "d",
	"n" => "n",
	"o" => "o",
);

function process_binary(&$list, $field, $value)
{
	foreach ($list as $index => $row) {
		if ($row[$field] == 1)
			$d = $value;
		else
			$d = "";
		$list[$index][$field] = $d;
	}
}

function climbs_command_line ($format, $def_format, $sort)
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

function climbs_browser_options ($format, $def_format, $sort)
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

function climbs_main($options)
{
	$last_update = date ("j M Y", strtotime (db_get_last_update()));

	$climber_id = 1;
	$table   = "route left join climbs on ((climbs.route_id = route.id) and (climber_id = {$climber_id})) left join colour on (route.colour = colour.id) left join panel on (route.panel = panel.id) left join grade on (route.grade = grade.id) left join v_panel on (route.panel = v_panel.number)";
	$columns = array ("route.id as id", "panel.number as panel", "colour.colour as colour", "grade.grade as grade", "grade.order as grade_num", "climber_id", "date_climbed", "v_panel.climb_type as climb_type", "success", "downclimb as d", "nice as n", "onsight as o", "difficulty as diff", "climbs.notes as notes");
	$where   = NULL;

	switch ($options["sort"]) {
		case "age":     $order = "date_climbed desc, panel, grade_num, colour"; $mark = "mark_date_climbed"; break;
		case "colour":  $order = "colour, panel, grade";                        $mark = "mark_colour";       break;
		case "grade":   $order = "grade_num, panel, colour";                    $mark = "mark_grade";        break;
		case "success": $order = "success, panel, grade, colour";               $mark = "mark_success";      break;
		case "type":    $order = "climb_type, panel, grade, colour";            $mark = "mark_climb_type";   break;
		case "d":       $order = "d desc, panel, grade, colour";                $mark = "mark_downclimb";    break;
		case "n":       $order = "n desc, panel, grade, colour";                $mark = "mark_nice";         break;
		case "o":       $order = "o desc, panel, grade, colour";                $mark = "mark_onsight";      break;
		default:        $order = "panel, grade_num, colour";                    $mark = "mark_panel";        break;
	}

	$list = db_select($table, $columns, $where, $order);
	$count = count($list);

	process_binary ($list, "d", "D");
	process_binary ($list, "n", "N");
	process_binary ($list, "o", "O");
	process_date ($list, "date_climbed", TRUE);
	process_type ($list);

	$columns = array ("panel", "colour", "grade", "date_climbed", "climb_type", "success", "d", "n", "o", "diff", "notes");

	// calculate widths (include headers?)
	$widths = column_widths ($list, $columns, TRUE);

	// alter justification of widths
	fix_justification ($widths);

	$output = "";
	//header("Pragma: no-cache");
	switch ($options["format"]) {
		case "html":
			$last_update = date ("j M Y", strtotime (db_get_last_update()));

			$output .= html_header ("Climbs", "../");
			$output .= "<body>";

			$output .= "<div class='download'>";
			$output .= "<h1>Route Data</h1>";
			$output .= "<a href='?format=text'><img src='../img/txt.png'></a>";
			$output .= "&nbsp;&nbsp;";
			$output .= "<a href='?format=csv'><img src='../img/ss.png'></a>";
			$output .= "</div>";

			$output .= "<div class='header'>Climbs <span>(Last updated: $last_update)</span></div>\n";
			$output .= html_menu("../");
			$output .= "<div class='content'>";
			$output .= "<h2>Climbs ({$count})</h2>";
			$output .= list_render_html ($list, $columns, $widths, $mark);
			$output .= "</div>";
			$output .= get_errors();
			$output .= "</body>";
			$output .= "</html>";
			break;

		case "csv":
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="climbs.csv"');
			$output .= list_render_csv ($list, $columns);
			break;

		case "text":
		default:
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="climbs.txt"');
			$output .= list_render_text ($list, $columns, $widths);
			break;
	}

	return $output;
}


date_default_timezone_set("UTC");

$format = array ("csv", "html", "text");
$sort   = array ("age", "colour", "grade", "panel", "success", "type", "d", "n", "o");

if (isset ($argc))
	$options = climbs_command_line ($format, 2, $sort);
else
	$options = climbs_browser_options ($format, 1, $sort);

echo climbs_main ($options);

