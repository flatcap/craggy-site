<?php

set_include_path ('../../libs');

include 'db.php';
include 'utils.php';

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
	include "dbnames.php";

	$last_update = date ("j M Y", strtotime (db_get_last_update()));

	$climber_id = 1;

	$table   = "$DB_ROUTE" .
			" left join $DB_CLIMB on (($DB_CLIMB.route_id = $DB_ROUTE.id) and (climber_id = {$climber_id}))" .
			" left join $DB_COLOUR on ($DB_ROUTE.colour_id = $DB_COLOUR.id)" .
			" left join $DB_PANEL on ($DB_ROUTE.panel_id = $DB_PANEL.id)" .
			" left join $DB_GRADE on ($DB_ROUTE.grade_id = $DB_GRADE.id)" .
			" left join $DB_CLIMB_TYPE on ($DB_PANEL.climb_type_id = $DB_CLIMB_TYPE.id)" .
			" left join $DB_SUCCESS on ($DB_CLIMB.success_id = $DB_SUCCESS.id)" .
			" left join $DB_DIFFICULTY on ($DB_CLIMB.difficulty_id = $DB_DIFFICULTY.id)";

	$columns = array ("$DB_ROUTE.id as id",
			"$DB_PANEL.name as panel",
			"$DB_PANEL.sequence as panel_seq",
			"$DB_COLOUR.colour as colour",
			"$DB_GRADE.grade as grade",
			"$DB_GRADE.sequence as grade_seq",
			"climber_id",
			"date_climbed",
			"climb_type",
			"$DB_SUCCESS.outcome as success",
			"nice as n",
			"onsight as o",
			"$DB_DIFFICULTY.description as diff",
			"$DB_CLIMB.notes as notes");

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


date_default_timezone_set('UTC');

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

