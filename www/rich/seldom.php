<?php

set_include_path ('../../libs');

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

function seldom_climbs ($ranges, $climber_id)
{
	include 'db_names.php';

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
			  "$DB_PANEL.name             as panel",
			  "$DB_COLOUR.colour          as colour",
			  "$DB_GRADE.grade            as grade",
			  "climb_type",
			  "date_climbed",
			  "success_id",
			  "$DB_SUCCESS.outcome        as success",
			  "nice                       as n",
			  "$DB_DIFFICULTY.description as diff",
			  "$DB_RATING.notes           as notes");

	$where   = array ('date_end is null', "$DB_GRADE.sequence < 600");
	$order   = "$DB_PANEL.sequence, $DB_GRADE.sequence, colour, date_climbed";

	// print data (based on column names)
	$list = db_select2($table, $columns, $where, $order);

	$list = process_best ($list);

	process_type ($list);
	process_date ($list, 'date_climbed', true);

	$today = strtotime('today');
	$results = array();
	foreach ($list as $index => $row) {
		$m = $row['months'];
		if (empty ($m))
			$m = 99;

		foreach ($ranges as $lower) {
			if ($m > $lower) {
				$results[$lower][] = $row;
				break;
			}
		}
	}

	return $results;
}

function seldom_main ($options, $climber_id)
{
	$output = '';
	$ranges = array (12, 6, 4, 3, 2);

	switch ($options['format']) {
		case 'html':
			$last_update = date ('j M Y', strtotime (db_get_last_update()));

			$output .= html_header ('Seldom', '../');
			$output .= '<body>';
			$output .= html_menu('../');
			$output .= "<div class='content'>";

			$output .= "<div class='title'>";
			$output .= "<h1>Seldom</h1> <span>(Last updated: $last_update)</span>";
			$output .= "<span class='download'>";
			$output .= '<h3>Route Data</h3>';
			$output .= "<a href='?format=text'><img alt='seldom list as a formatted text document' width='32' height='32' src='../img/txt.png'></a>";
			$output .= "<a href='?format=csv'><img alt='seldom list as a csv document' width='32' height='32' src='../img/ss.png'></a>";
			$output .= '</span>';
			$output .= '</div>';
			break;

		case 'csv':
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="seldom.csv"');
			break;

		case 'text':
		default:
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="seldom.txt"');
			break;
	}

	$list = seldom_climbs ($ranges, $climber_id);

	$total = 0;
	foreach ($ranges as $lower) {
		if (!array_key_exists ($lower, $list))
			continue;
		$l = $list[$lower];
		$count = count ($l);
		if ($count == 0)
			continue;
		$total += $count;

		$columns = array ('panel', 'colour', 'grade', 'climb_type', 'success', 'notes', 'date_climbed', 'age', 'months');
		$widths = column_widths ($l, $columns, true);
		fix_justification ($widths);

		// render section
		switch ($options['format']) {
			case 'html':
				$output .= "<h2>$lower months <span>($count climbs)</span></h2>";
				$output .= list_render_html ($l, $columns, $widths, '{sortlist: [[0,0], [2,0], [1,0]]}');
				$output .= '<br>';
				break;

			case 'csv':
				$output .= list_render_csv ($l, $columns);
				$output .= '""' . "\r\n";
				break;

			case 'text':
			default:
				$output .= "$lower months ($count climbs)\r\n";
				$output .= list_render_text ($l, $columns, $widths);
				$output .= "\r\n";
				break;
		}
	}

	switch ($options['format']) {
		case 'html':
			$output .= '</div>';
			$output .= get_errors();
			$output .= '</body>';
			$output .= '</html>';
			break;

		case 'csv':
		case 'text':
		default:
			break;
	}

	return $output;
}


date_default_timezone_set('UTC');

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
echo seldom_main ($options, $climber_id);

