<?php

set_include_path ('../libs');

include_once 'db.php';
include_once 'utils.php';

function process_best ($list)
{
	$result = array();

	$id = 0;
	foreach ($list as $row) {
		if ($row['route_id'] == $id) {
			// duplicate
			$d1 = $result[$id]['date_climbed'];
			$d2 = $row['date_climbed'];
			if ($d2 > $d1) {
				$result[$id]['date_climbed'] = $d2;
			}

			$s1 = intval ($result[$id]['success_id']);
			$s2 = intval ($row['success_id']);
			if ($s2 > $s1) {
				$result[$id]['success_id'] = $s2;
				$result[$id]['success'] = $row['success'];
			}

			if ($row['notes'])
				$results[$id]['notes'] = $row['notes'];
		} else {
			// copy the whole row
			$id = $row['route_id'];
			$result[$id] = $row;
			$result[$id]['o'] = ($row['success_id'] > 2);	// Generate the onsight column
		}
	}

	return $result;
}

function process_binary(&$list, $field, $value)
{
	foreach ($list as $index => $row) {
		if ($row[$field] == 1)
			$d = $value;
		else
			$d = '';
		$list[$index][$field] = $d;
	}
}

function climbs_main ($options, $climber_id)
{
	include 'db_names.php';

	$db = db_get_database();

	$table   = $DB_ROUTE .
			" left join $DB_CLIMB      on (($DB_CLIMB.route_id      = $DB_ROUTE.id) and (climber_id = {$climber_id}))" .
			" left join $DB_COLOUR     on ($DB_ROUTE.colour_id      = $DB_COLOUR.id)" .
			" left join $DB_PANEL      on ($DB_ROUTE.panel_id       = $DB_PANEL.id)" .
			" left join $DB_GRADE      on ($DB_ROUTE.grade_id       = $DB_GRADE.id)" .
			" left join $DB_CLIMB_TYPE on ($DB_PANEL.climb_type_id  = $DB_CLIMB_TYPE.id)" .
			" left join $DB_SUCCESS    on ($DB_CLIMB.success_id     = $DB_SUCCESS.id)" .
			" left join $DB_RATING     on ($DB_RATING.route_id      = $DB_ROUTE.id)" .
			" left join $DB_DIFFICULTY on ($DB_RATING.difficulty_id = $DB_DIFFICULTY.id)";

	$columns = array (
			  "$DB_ROUTE.id               as route_id",
		//	  "$DB_CLIMB.id               as climb_id",
			  "$DB_PANEL.name             as panel",
		//	  "$DB_PANEL.sequence         as panel_seq",
			  "$DB_COLOUR.colour          as colour",
			  "$DB_GRADE.grade            as grade",
		//	  "$DB_GRADE.sequence         as grade_seq",
			  "climb_type",
			  "date_climbed",
			  "success_id",
			  "$DB_SUCCESS.outcome        as success",
			  "nice                       as n",
			  "$DB_DIFFICULTY.description as diff",
			  "$DB_RATING.notes           as notes");

	$where   = array ('date_end is null');
	$order   = "$DB_PANEL.sequence, $DB_GRADE.sequence, colour, date_climbed";

	$list = db_select2($db, $table, $columns, $where, $order);

	$list = process_best ($list);

	process_binary ($list, 'n', 'N');
	process_binary ($list, 'o', 'O');
	process_date ($list, 'date_climbed', true);
	process_type ($list);

	$columns = array ('panel', 'colour', 'grade', 'climb_type', 'success', 'n', 'o', 'diff', 'notes', 'date_climbed', 'months');

	// calculate widths (include headers?)
	$widths = column_widths ($list, $columns, true);

	// alter justification of widths
	fix_justification ($widths);

	$output = '';
	switch ($options['format']) {
		case 'html':
			$last_update = date ('j M Y', strtotime (db_get_last_update($db)));

			$output .= html_header ('Climbs', '../');
			$output .= '<body>';
			$output .= html_menu('../');

			$output .= "<div class='content'>";

			$output .= "<div class='title'>";
			$output .= "<h1>Climbs</h1>";
			$output .= "<span class='download'>";
			$output .= '<h3>Route Data</h3>';
			$output .= "<a href='?format=text'><img alt='climb list as a formatted text document' width='32' height='32' src='../style/txt.png'></a>";
			$output .= "<a href='?format=csv'><img alt='climb list as a csv document' width='32' height='32' src='../style/ss.png'></a>";
			$output .= '</span>';
			$output .= '</div>';

			$output .= list_render_html ($list, $columns, $widths, '{sortlist: [[0,0], [2,0], [1,0]]}');
			$output .= '</div>';
			$output .= get_errors();
			$output .= html_footer();
			break;

		case 'csv':
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="climbs.csv"');
			$output .= list_render_csv ($list, $columns);
			break;

		case 'text':
		default:
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="climbs.txt"');
			$output .= list_render_text ($list, $columns, $widths);
			break;
	}

	return $output;
}


date_default_timezone_set('Europe/London');

$format = array ('csv', 'html', 'text');

if (isset ($argc)) {
		$longopts = array('format:');

		$options = getopt(null, $longopts);

		if (!array_key_exists ('format', $options) || !in_array ($options['format'], $format)) {
			$options['format'] = $format[2];
		}
} else {
		$options = array();

		$f = get_url_variable ('format');
		if (!in_array ($f, $format))
			$f = $format[1];

		$options['format'] = $f;
}

$climber_id = 1;
echo climbs_main ($options, $climber_id);

