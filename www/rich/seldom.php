<?php

set_include_path (".:..");

include "db.php";
include "utils.php";

function seldom_range ($m_start, $m_finish, $options)
{
	$output = "";

	$when_start  = db_date ("$m_start months ago");

	$climber_id = 1;

	$table   = "craggy_route" .
			" left join craggy_climb on ((craggy_climb.route_id = craggy_route.id) and (climber_id = {$climber_id}))" .
			" left join craggy_colour on (craggy_route.colour_id = craggy_colour.id)" .
			" left join craggy_panel on (craggy_route.panel_id = craggy_panel.id)" .
			" left join craggy_grade on (craggy_route.grade_id = craggy_grade.id)" .
			" left join v_panel on (craggy_route.panel_id = v_panel.name)" .
			" left join craggy_success on (craggy_climb.success_id = craggy_success.id)" .
			" left join craggy_difficulty on (craggy_climb.difficulty_id = craggy_difficulty.id)";

	$columns = array ("craggy_route.id as id",
			"craggy_panel.name as panel",
			"craggy_panel.sequence as panel_num",
			"craggy_colour.colour as colour",
			"craggy_grade.grade as grade",
			"craggy_grade.sequence as grade_num",
			"climber_id",
			"date_climbed",
			"v_panel.climb_type as climb_type",
			"craggy_success.outcome as success",
			"nice as n",
			"onsight as o",
			"craggy_difficulty.description as diff",
			"craggy_climb.notes as notes");

	$where   = array ("craggy_grade.sequence < 600");
	$order = "panel_num, grade_num, colour";

	if (isset ($m_finish)) {
		array_push ($where, "date_climbed < '$when_start'");
		$when_finish = db_date ("$m_finish months ago");
		array_push ($where, "date_climbed > '$when_finish'");
	} else {
		array_push ($where, "((date_climbed < '$when_start') or (date_climbed is null))");
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

echo seldom_main ($options);

