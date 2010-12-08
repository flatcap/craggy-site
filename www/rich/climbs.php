<?php

set_include_path (".:..");

include "db.php";
include "utils.php";
include "mark.php";

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

function climbs_main($options)
{
	$last_update = date ("j M Y", strtotime (db_get_last_update()));

	$climber_id = 1;

	$table   = "craggy_route" .
			" left join craggy_climbs on ((craggy_climbs.route_id = craggy_route.id) and (climber_id = {$climber_id}))" .
			" left join craggy_colour on (craggy_route.colour = craggy_colour.id)" .
			" left join craggy_panel on (craggy_route.panel = craggy_panel.id)" .
			" left join craggy_grade on (craggy_route.grade = craggy_grade.id)" .
			" left join v_panel on (craggy_route.panel = v_panel.number)" .
			" left join craggy_success on (craggy_climbs.success = craggy_success.id)" .
			" left join craggy_difficulty on (craggy_climbs.difficulty = craggy_difficulty.id)";

	$columns = array ("craggy_route.id as id",
			"craggy_panel.number as panel",
			"craggy_colour.colour as colour",
			"craggy_grade.grade as grade",
			"craggy_grade.order as grade_num",
			"climber_id",
			"date_climbed",
			"v_panel.climb_type as climb_type",
			"craggy_success.outcome as success",
			"nice as n",
			"onsight as o",
			"craggy_difficulty.description as diff",
			"craggy_climbs.notes as notes");
	$where   = NULL;
	$order   = "panel, grade_num, colour";

	$list = db_select($table, $columns, $where, $order);
	$count = count($list);

	process_binary ($list, "n", "N");
	process_binary ($list, "o", "O");
	process_date ($list, "date_climbed", TRUE);
	process_type ($list);

	$columns = array ("panel", "colour", "grade", "date_climbed", "climb_type", "success", "n", "o", "diff", "notes");

	// calculate widths (include headers?)
	$widths = column_widths ($list, $columns, TRUE);

	// alter justification of widths
	fix_justification ($widths);

	$output = "";
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
			$output .= list_render_html ($list, $columns, $widths);
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

echo climbs_main ($options);

