<?php

set_include_path ("../../libs");

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
			" left join craggy_climb on ((craggy_climb.route_id = craggy_route.id) and (climber_id = {$climber_id}))" .
			" left join craggy_colour on (craggy_route.colour_id = craggy_colour.id)" .
			" left join craggy_panel on (craggy_route.panel_id = craggy_panel.id)" .
			" left join craggy_grade on (craggy_route.grade_id = craggy_grade.id)" .
			" left join craggy_climb_type on (craggy_panel.climb_type_id = craggy_climb_type.id)" .
			" left join craggy_success on (craggy_climb.success_id = craggy_success.id)" .
			" left join craggy_difficulty on (craggy_climb.difficulty_id = craggy_difficulty.id)";

	$columns = array ("craggy_route.id as id",
			"craggy_panel.name as panel",
			"craggy_panel.sequence as panel_seq",
			"craggy_colour.colour as colour",
			"craggy_grade.grade as grade",
			"craggy_grade.sequence as grade_seq",
			"climber_id",
			"date_climbed",
			"climb_type",
			"craggy_success.outcome as success",
			"nice as n",
			"onsight as o",
			"craggy_difficulty.description as diff",
			"craggy_climb.notes as notes");

	$where   = NULL;
	$order   = "panel_seq, grade_seq, colour";

	$list = db_select($table, $columns, $where, $order);
	$count = count($list);

	process_binary ($list, "n", "N");
	process_binary ($list, "o", "O");
	process_date ($list, "date_climbed", TRUE);
	process_type ($list);

	$columns = array ("panel", "colour", "grade", "climb_type", "date_climbed", "success", "n", "o", "diff", "notes");

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
			$output .= "<a href='?format=text'><img alt='climb list as a formatted text document' width='24' height='24' src='../img/txt.png'></a>";
			$output .= "&nbsp;&nbsp;";
			$output .= "<a href='?format=csv'><img alt='climb list as a csv document' width='24' height='24' src='../img/ss.png'></a>";
			$output .= "</div>";

			$output .= "<div class='header'>Climbs <span>(Last updated: $last_update)</span></div>\n";
			$output .= html_menu("../");
			$output .= "<div class='content'>";
			$output .= "<h2>Climbs <span>({$count})</span></h2>";
			$output .= list_render_html ($list, $columns, $widths, "{sortlist: [[0,0], [2,0], [1,0]]}");
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

