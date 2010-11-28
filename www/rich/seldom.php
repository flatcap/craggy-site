<?php

set_include_path (".:..");

include "db.php";
include "utils.php";
include "mark.php";

$g_col_sort   = array (
	"climbed" => "age",
	"colour" => "colour",
	"grade" => "grade",
	"months" => "age",
	"panel" => "panel",
	"success" => "success",
	"type" => "type"
);

function seldom_command_line ($format, $def_format, $sort)
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

function seldom_browser_options ($format, $def_format, $sort)
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

function seldom_range ($m_start, $m_finish, $options)
{
	$output = "";

	$when_start  = db_date ("$m_start months ago");

	$climber_id = 1;
	$table   = "route left join climbs on ((climbs.route_id = route.id) and (climber_id = {$climber_id})) left join colour on (route.colour = colour.id) left join panel on (route.panel = panel.id) left join grade on (route.grade = grade.id) left join v_panel on (route.panel = v_panel.number)";
	$columns = array ("route.id as id", "panel.number as panel", "colour.colour as colour", "grade.grade as grade", "grade.order as grade_num", "climber_id", "date_climbed", "v_panel.climb_type as climb_type", "success", "downclimb as d", "nice as n", "onsight as o", "difficulty as diff", "climbs.notes as notes");
	$where   = array ("grade.order < 600");

	if (isset ($m_finish)) {
		array_push ($where, "date_climbed < '$when_start'");
		$when_finish = db_date ("$m_finish months ago");
		array_push ($where, "date_climbed > '$when_finish'");
	} else {
		array_push ($where, "((date_climbed < '$when_start') or (date_climbed is null))");
	}

	switch ($options["sort"]) {
		case "age":     $order = "date_climbed, panel, grade_num, colour"; break;
		case "colour":  $order = "colour, panel, grade";                   break;
		case "grade":   $order = "grade_num, panel, colour";               break;
		case "success": $order = "success, panel, grade, colour";          break;
		case "type":    $order = "climb_type, panel, grade, colour";       break;
		default:        $order = "panel, grade_num, colour";               break;
	}

	// print data (based on column names)
	$list = db_select($table, $columns, $where, $order);

	$today = strtotime("today");
	// manipulate data (Lead -> L)
	foreach ($list as $index => $row) {
		if ($row['climb_type'] == "Lead")
			$list[$index]['climb_type'] = "L";
		else
			$list[$index]['climb_type'] = "";

		$d = $row["date_climbed"];
		if ($d == "0000-00-00")
			$d = "";
		$list[$index]["date_climbed"] = $d;

		if (empty($d)) {
			$m = "";
		} else {
			$a = floor (($today - strtotime($d)) / 86400);
			$m = sprintf ("%.1f", $a / 30.44);
		}

		$list[$index]["months"] = $m;
	}

	array_push ($columns, "months");
	unset ($columns[6]);

	return $list;
}

function seldom_main ($options)
{
	$output = "";
	$ranges = array (12, 6, 4, 3, 2);

	//header("Pragma: no-cache");
	switch ($options["format"]) {
		case "html":
			$last_update = date ("j M Y", strtotime (db_get_last_update()));

			$output .= html_header ("Seldom", "../");
			$output .= "<body>";

			$output .= "<div class='download'>";
			$output .= "<h1>Route Data</h1>";
			$output .= "<a href='?format=text'><img src='../img/txt.png'></a>";
			$output .= "&nbsp;&nbsp;";
			$output .= "<a href='?format=csv'><img src='../img/ss.png'></a>";
			$output .= "</div>";

			$output .= "<div class='header'>Seldom <span>(Last updated: $last_update)</span></div>";
			$output .= html_menu("../");
			$output .= "<div class='content'>";
			break;

		case "csv":
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="seldom.csv"');
			break;

		case "text":
		default:
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="seldom.txt"');
			break;
	}

	$start  = NULL;
	$finish = NULL;
	foreach ($ranges as $num) {
		$finish = $start;
		$start  = $num;

		$list = seldom_range ($start, $finish, $options);
		$count = count ($list);
		if ($count == 0)
			continue;

		process_date ($list, "date_climbed", FALSE);

		$columns = array ("panel", "colour", "grade", "climb_type", "success", "notes", "date_climbed");
		$widths = column_widths ($list, $columns, TRUE);
		fix_justification ($widths);

		// render section
		switch ($options["format"]) {
			case "html":
				$output .= "<h2>$start-$finish months <span>($count climbs)</span></h2>\n";
				$output .= list_render_html ($list, $columns, $widths);
				$output .= "<br>";
				break;

			case "csv":
				$output .= list_render_csv ($list, $columns);
				$output .= '""' . "\r\n";
				break;

			case "text":
			default:
				$output .= "$start-$finish months ($count climbs)\r\n";
				$output .= list_render_text ($list, $columns, $widths);
				$output .= "\r\n";
				break;
		}
	}

	switch ($options["format"]) {
		case "html":
			$output .= "</div>";
			$output .= get_errors();
			$output .= "</body>";
			$output .= "</html>";
			break;

		case "csv":
			break;

		case "text":
		default:
			break;
	}

	return $output;
}


date_default_timezone_set("UTC");

$format = array ("csv", "html", "text");
$sort   = array ("age", "colour", "grade", "panel", "success", "type");

if (isset ($argc))
	$options = seldom_command_line ($format, 2, $sort);
else
	$options = seldom_browser_options ($format, 1, $sort);

echo seldom_main ($options);

