<?php

set_include_path (".:..");

include "db.php";
include "utils.php";

function downclimb_main ($options)
{
	$table   = "v_routes";

	$climber_id = 1;
	$table   = "route left join climbs on ((climbs.route_id = route.id) and (climber_id = {$climber_id})) left join colour on (route.colour = colour.id) left join panel on (route.panel = panel.id) left join grade on (route.grade = grade.id) left join v_panel on (route.panel = v_panel.number) left join difficulty on (climbs.difficulty = difficulty.id)";
	$columns = array ("route.id as id", "panel.number as panel", "colour.colour as colour", "grade.grade as grade", "grade.order as grade_num", "climber_id", "date_climbed", "v_panel.climb_type as climb_type", "success", "nice as n", "onsight as o", "difficulty.description as difficulty", "climbs.notes as notes");
	$where   = array ("success <> 4", "grade.order < 400");
	$order = "panel, grade_num, colour";

	$list = db_select($table, $columns, $where, $order);

	// manipulate data (Lead -> L)
	foreach ($list as $key => $row) {
		if ($row['climb_type'] == "Lead")
			$list[$key]['climb_type'] = "L";
		else
			$list[$key]['climb_type'] = "";
	}

	$columns = array ("panel", "colour", "grade", "climb_type", "difficulty", "notes");

	// calculate widths (include headers?)
	$widths = column_widths ($list, $columns, TRUE);

	// alter justification of widths
	fix_justification ($widths);

	$count  = count ($list);
	$output = "";
	switch ($options["format"]) {
		case "html":
			$last_update = date ("j M Y", strtotime (db_get_last_update()));

			$output .= html_header ("Downclimbs", "../");
			$output .= "<body>";

			$output .= "<div class='download'>";
			$output .= "<h1>Route Data</h1>";
			$output .= "<a href='?format=text'><img src='../img/txt.png'></a>";
			$output .= "&nbsp;&nbsp;";
			$output .= "<a href='?format=csv'><img src='../img/ss.png'></a>";
			$output .= "</div>";

			$output .= "<div class='header'>Downclimbs <span>(Last updated: $last_update)</span></div>\n";
			$output .= html_menu("../");
			$output .= "<div class='content'>\n";
			$output .= "<h2>Downclimb <span>($count climbs)</span></h2>\n";
			$output .= list_render_html ($list, $columns, $widths);
			$output .= "</div>";
			$output .= get_errors();
			$output .= "</body>";
			$output .= "</html>";
			break;

		case "csv":
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="downclimbs.csv"');
			$output .= list_render_csv ($list, $columns);
			break;

		case "text":
		default:
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="downclimbs.txt"');
			$output .= "Downclimb ($count climbs)\n";
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

echo downclimb_main($options);

