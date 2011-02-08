<?php

set_include_path ('../../libs');

include 'db.php';
include 'utils.php';

function cmp_panel2($a, $b)
{
	$s1 = $a['score'];
	$p1 = $a['panel'];
	$g1 = $a['grade_seq'];
	$c1 = $a['colour'];

	$s2 = $b['score'];
	$p2 = $b['panel'];
	$g2 = $b['grade_seq'];
	$c2 = $b['colour'];

	if ($s1 != $s2)
		return ($s1 > $s2) ? -1 : 1;

	if ($p1 != $p2)
		return ($p1 < $p2) ? -1 : 1;

	if ($g1 != $g2)
		return ($g1 < $g2) ? -1 : 1;

	return ($c1 < $c2) ? -1 : 1;
}

function work_score (&$list)
{
	$total = 0;
	$scores = array();

	foreach ($list as $row) {
		$p = $row['panel'];
		switch ($row['priority']) {
			case '2': $score = 1; break;
			case '3': $score = 2; break;
			case '4': $score = 4; break;
			case 'D': $score = 6; break;
			case 'T': $score = 8; break;
			default:  $score = 1; break;
		}

		if (array_key_exists ($p, $scores)) {
			$scores[$p] += $score;
		} else {
			$scores[$p] = $score;
		}
		$total += $score;
	}

	foreach ($list as $index => $row) {
		$p = $row['panel'];
		$list[$index]['score'] = $scores[$p];
	}

	return $total;
}


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

function work_all_climbs ($climber_id)
{
	include 'db_names.php';

	$table = $DB_ROUTE .
			" left join $DB_CLIMB      on (($DB_CLIMB.route_id      = $DB_ROUTE.id) and (climber_id = {$climber_id}))" .
			" left join $DB_COLOUR     on ($DB_ROUTE.colour_id      = $DB_COLOUR.id)" .
			" left join $DB_PANEL      on ($DB_ROUTE.panel_id       = $DB_PANEL.id)" .
			" left join $DB_GRADE      on ($DB_ROUTE.grade_id       = $DB_GRADE.id)" .
			" left join $DB_CLIMB_TYPE on ($DB_PANEL.climb_type_id  = $DB_CLIMB_TYPE.id)" .
			" left join $DB_SUCCESS    on ($DB_CLIMB.success_id     = $DB_SUCCESS.id)" .
			" left join $DB_RATING     on ($DB_RATING.route_id      = $DB_ROUTE.id)" .
			" left join $DB_DIFFICULTY on ($DB_RATING.difficulty_id = $DB_DIFFICULTY.id)" .
			" left join $DB_CLIMB_NOTE on ($DB_RATING.climb_note_id = $DB_CLIMB_NOTE.id)";

	$columns = array ("$DB_ROUTE.id               as route_id",
			  "$DB_PANEL.name             as panel",
			  "$DB_COLOUR.colour          as colour",
			  "$DB_GRADE.grade            as grade",
			  "$DB_GRADE.sequence         as grade_seq",
			  "climb_type",
			  "date_climbed",
			  "success_id",
			  "$DB_SUCCESS.outcome        as success",
			  "nice                       as n",
			  "$DB_DIFFICULTY.description as diff",
			  "notes");

	$where   = array ('date_end is null');

	$list = db_select2($table, $columns, $where);
	$list = process_best ($list);

	$today = strtotime('today');
	foreach ($list as $index => &$row) {
		$s = $row['success_id'];
		$g = $row['grade_seq'];

		$d = $row['date_climbed'];
		if (empty ($d) || ($d == '0000-00-00')) {
			$m = '999';
		} else {
			$a = floor (($today - strtotime($d)) / 86400);
			$m = sprintf ('%.1f', $a / 30.44);
		}

		if ((($s < 3) || empty ($s)) && ($g < 600)) {
			$row['priority'] = 'T';			// to do
		} else if (($s != 4) && ($g < 400)) {
			$row['priority'] = 'D';			// downclimb
		} else if ($g < 600) {
			if ($m >= 6)
				$row['priority'] = '6';		// 6 months
			else if ($m >= 4)
				$row['priority'] = '4';		// 4 months
			else if ($m >= 3)
				$row['priority'] = '3';		// 3 months
			else if ($m >= 2)
				$row['priority'] = '2';		// 2 months
		}
		if (!array_key_exists ('priority', $row)) {
			unset ($list[$index]);
		}
	}

	return $list;
}


function work_main ($options, $climber_id)
{
	$all = work_all_climbs ($climber_id);
	$score = work_score ($all);

	$cmp = 'cmp_panel2';
	usort ($all, $cmp);

	process_type ($all);

	$columns = array ('panel', 'colour', 'grade', 'climb_type', 'success', 'notes', 'priority', 'score');
	$widths = column_widths ($all, $columns, true);
	fix_justification ($widths);

	$count  = count ($all);
	$output = '';
	switch ($options['format']) {
		case 'html':
			$last_update = date ('j M Y', strtotime (db_get_last_update()));

			$output .= html_header ('Work', '../');
			$output .= '<body>';
			$output .= html_menu('../');
			$output .= "<div class='content'>\n";

			$output .= "<div class='title'>";
			$output .= "<h1>Work</h1> <span>(Last updated: $last_update)</span>";
			$output .= "<span class='download'>";
			$output .= '<h3>Route Data</h3>';
			$output .= "<a href='?format=text'><img alt='work list as a formatted text document' width='32' height='32' src='../img/txt.png'></a>";
			$output .= "<a href='?format=csv'><img alt='work list as a csv document' width='32' height='32' src='../img/ss.png'></a>";
			$output .= '</span>';
			$output .= '</div>';

			$output .= "<h2>Work <span>($count climbs)</span><span> (Score = $score)</span></h2>\n";
			$output .= list_render_html ($all, $columns, $widths, '{sortlist: [[7,1],[0,0], [2,0], [1,0]]}');
			$output .= '</div>';
			$output .= get_errors();
			$output .= '</body>';
			$output .= '</html>';
			break;

		case 'csv':
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="work.csv"');
			$output .= list_render_csv ($all, $columns);
			break;

		case 'text':
		default:
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="work.txt"');
			$output .= "Work ($count climbs)\r\n";
			$output .= list_render_text ($all, $columns, $widths);
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
echo work_main($options, $climber_id);

