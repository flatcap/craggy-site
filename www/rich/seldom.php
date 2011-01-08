<?php

set_include_path ('../../libs');

include 'db.php';
include 'utils.php';

function seldom_range ($m_start, $m_finish, $options)
{
	include 'dbnames.php';

	$output = '';

	$when_start  = db_date ("$m_start months ago");

	$climber_id = 1;

	$table   = $DB_ROUTE .
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
			'climber_id',
			'date_climbed',
			'climb_type',
			"$DB_SUCCESS.outcome as success",
			'nice as n',
			'onsight as o',
			"$DB_DIFFICULTY.description as diff",
			"$DB_CLIMB.notes as notes");

	$where   = array ("$DB_GRADE.sequence < 600");
	$order = 'panel_seq, grade_seq, colour';

	if (isset ($m_finish)) {
		array_push ($where, "date_climbed < '$when_start'");
		$when_finish = db_date ("$m_finish months ago");
		array_push ($where, "date_climbed > '$when_finish'");
	} else {
		array_push ($where, "((date_climbed < '$when_start') or (date_climbed is null))");
	}

	// print data (based on column names)
	$list = db_select($table, $columns, $where, $order);

	$today = strtotime('today');
	// manipulate data (Lead -> L)
	foreach ($list as $index => $row) {
		if ($row['climb_type'] == 'Lead')
			$list[$index]['climb_type'] = 'L';
		else
			$list[$index]['climb_type'] = '';

		$d = $row['date_climbed'];
		if ($d == '0000-00-00')
			$d = '';
		$list[$index]['date_climbed'] = $d;

		if (empty($d)) {
			$m = '';
		} else {
			$a = floor (($today - strtotime($d)) / 86400);
			$m = sprintf ('%.1f', $a / 30.44);
		}

		$list[$index]['months'] = $m;
	}

	array_push ($columns, 'months');
	unset ($columns[6]);

	return $list;
}

function seldom_main ($options)
{
	$output = '';
	$ranges = array (12, 6, 4, 3, 2);

	switch ($options['format']) {
		case 'html':
			$last_update = date ('j M Y', strtotime (db_get_last_update()));

			$output .= html_header ('Seldom', '../');
			$output .= '<body>';

			$output .= "<div class='download'>";
			$output .= '<h1>Route Data</h1>';
			$output .= "<a href='?format=text'><img alt='seldom list as a formatted text document' width='24' height='24' src='../img/txt.png'></a>";
			$output .= '&nbsp;&nbsp;';
			$output .= "<a href='?format=csv'><img alt='seldom list as a csv document' width='24' height='24' src='../img/ss.png'></a>";
			$output .= '</div>';

			$output .= "<div class='header'>Seldom <span>(Last updated: $last_update)</span></div>";
			$output .= html_menu('../');
			$output .= "<div class='content'>";
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

	$start  = NULL;
	$finish = NULL;
	foreach ($ranges as $num) {
		$finish = $start;
		$start  = $num;

		$list = seldom_range ($start, $finish, $options);
		$count = count ($list);
		if ($count == 0)
			continue;

		process_date ($list, 'date_climbed', FALSE);

		$columns = array ('panel', 'colour', 'grade', 'climb_type', 'success', 'notes', 'date_climbed');
		$widths = column_widths ($list, $columns, TRUE);
		fix_justification ($widths);

		// render section
		switch ($options['format']) {
			case 'html':
				$output .= "<h2>$start-$finish months <span>($count climbs)</span></h2>\n";
				$output .= list_render_html ($list, $columns, $widths, '{sortlist: [[0,0], [2,0], [1,0]]}');
				$output .= '<br>';
				break;

			case 'csv':
				$output .= list_render_csv ($list, $columns);
				$output .= '""' . "\r\n";
				break;

			case 'text':
			default:
				$output .= "$start-$finish months ($count climbs)\r\n";
				$output .= list_render_text ($list, $columns, $widths);
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
			break;

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

	$options = getopt(NULL, $longopts);

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

echo seldom_main ($options);

