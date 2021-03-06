<?php

set_include_path ('libs');

include_once 'db.php';
include_once 'utils.php';

include 'db_names.php';

function stats_grade_table ($grade_list, $whole_grades = false)
{
	$output = '';

	if ($whole_grades)
		$divisor = 100;
	else
		$divisor = 1;

	$results = array();
	foreach ($grade_list as $row) {
		$grade = $row['grade'];
		$gnum = intval ($row['grade_seq'] / $divisor);

		if (!array_key_exists ($gnum, $results)) {
			$results[$gnum] = array();
			$results[$gnum]['grade'] = $grade;
			$results[$gnum]['count'] = 0;
			$results[$gnum]['top_rope'] = 0;
			$results[$gnum]['lead'] = 0;
		}

		if ($row['climb_type'] == 'Lead')
			$results[$gnum]['lead']++;
		else
			$results[$gnum]['top_rope']++;
		$results[$gnum]['count']++;
	}

	$columns = array ('grade', 'count', 'top_rope', 'lead');
	$widths = column_widths ($results, $columns, true);
	fix_justification ($widths);

	$output .= list_render_html ($results, $columns, $widths, '{sortlist: [[0,0]]}');
	return $output;
}

function stats_grade_mean ($db, $grade_list)
{
	global $DB_GRADE;

	$output = '';

	$g_both = 0; $c_both = 0;
	$g_lead = 0; $c_lead = 0;
	$g_topr = 0; $c_topr = 0;

	foreach ($grade_list as $route) {

		$g_num = $route['grade_seq'];

		$g_both += $g_num;
		$c_both++;

		if ($route['climb_type'] == 'Lead') {
			$g_lead += $g_num;
			$c_lead++;
		} else {
			$g_topr += $g_num;
			$c_topr++;
		}
	}

	$g_both = round ($g_both / $c_both);
	$g_lead = round ($g_lead / $c_lead);
	$g_topr = round ($g_topr / $c_topr);

	$table   = $DB_GRADE;
	$columns = array('id', 'grade', 'sequence');
	$order   = 'sequence';

	$grades = db_select ($db, $table, $columns, null, $order);

	$output .= '<h2>Average Grades</h2>';
	$output .= '<b>All Routes</b>:<br>';
	$g_both = ($g_both / 2) - 25;
	$output .= "<div class='grade'><div class='marker' style='margin-left: {$g_both}px'></div></div><br>";

	$output .= '<b>Top Ropes</b>:<br>';
	$g_topr = ($g_topr / 2) - 25;
	$output .= "<div class='grade'><div class='marker' style='margin-left: {$g_topr}px'></div></div><br>";

	$output .= '<b>Lead Routes</b>:<br>';
	$g_lead = ($g_lead / 2) - 25;
	$output .= "<div class='grade'><div class='marker' style='margin-left: {$g_lead}px'></div></div><br>";

	return $output;
}

function stats_grade($db)
{
	global $DB_V_ROUTE;

	$output = '';

	$table   = $DB_V_ROUTE;
	$columns = array ('id', 'grade', 'grade_seq', 'climb_type');
	$where   = null;
	$order   = 'grade_seq';

	$grade_list = db_select($db, $table, $columns, $where, $order);

	$output .= stats_grade_mean ($db, $grade_list);
	$output .= '<br>';
	$output .= "<div class='graph'>";
	$output .= "<img alt='graph of grade vs frequency' width='800' height='400' src='style/grade.png'>";
	$output .= '</div>';
	$output .= '<br>';
	$output .= stats_grade_table ($grade_list, false);

	return $output;
}

function stats_main()
{
	$type = get_url_variable('type');

	$db = db_get_database();

	$last_update = date ('j M Y', strtotime (db_get_last_update($db)));

	$output  = '<body>';
	$output .= '<div style="background: red; color: white; text-align: center;">This is historic climb data</div>';
	$output .= html_menu();

	$output .= "<div class='content'>\n";
	$output .= "<div class='title'>";
	$output .= "<h1>Craggy Routes</h1>";
	$output .= '</div>';

	$output .= stats_grade($db);
	$output .= '</div>';
	$output .= get_errors();
	$output .= html_footer();

	$header  = html_header ('Grades');

	return $header . $output;
}


date_default_timezone_set('Europe/London');

echo stats_main();

