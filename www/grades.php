<?php

include "db.php";
include "utils.php";

function stats_grade_table ($grade_list, $whole_grades = FALSE)
{
	$output = "";

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
			$results[$gnum]['T'] = 0;
			$results[$gnum]['L'] = 0;
		}

		if ($row['climb_type'] == "Lead")
			$results[$gnum]['L']++;
		else
			$results[$gnum]['T']++;
	}

	$output  = "<table border='1' cellpadding='3' cellspacing='0'>";
	$output .= "<tr>";
	$output .= "<th>Grade</th>";
	$output .= "<th>Count</th>";
	$output .= "<th>Top Rope</th>";
	$output .= "<th>Lead</th>";
	$output .= "<th>Total</th>";
	$output .= "</tr>";

	$total = 0;
	foreach ($results as $gnum => $info) {

		$t = $info['T'];
		$l = $info['L'];
		$g = $info['grade'];
		$both = $t + $l;
		$total += $both;
		$output .= "<tr>";
		$output .= "<td>{$g}</td>";
		$output .= "<td>{$both}</td>";
		$output .= "<td>{$t}</td>";
		$output .= "<td>{$l}</td>";
		$output .= "<td>{$total}</td>";
		$output .= "</tr>";
	}
	$output .= "</table>";
	return $output;
}

function stats_grade_mean ($grade_list)
{
	$output = "";

	$g_both = 0; $c_both = 0;
	$g_lead = 0; $c_lead = 0;
	$g_topr = 0; $c_topr = 0;

	foreach ($grade_list as $route) {

		$g_num = $route['grade_seq'];

		$g_both += $g_num;
		$c_both++;

		if ($route['climb_type'] == "Lead") {
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

	$table   = "craggy_grade";
	$columns = array("id", "grade", "sequence");
	$order   = "sequence";

	$grades = db_select ($table, $columns, NULL, $order);

	$output .= "<h2>Average Grades</h2>";
	$output .= "<b>All Routes</b>:<br>";
	$g_both = ($g_both / 2) - 25;
	$output .= "<div class='grade'><div class='marker' style='margin-left: {$g_both}px'></div></div><br>";

	$output .= "<b>Top Ropes</b>:<br>";
	$g_topr = ($g_topr / 2) - 25;
	$output .= "<div class='grade'><div class='marker' style='margin-left: {$g_topr}px'></div></div><br>";

	$output .= "<b>Lead Routes</b>:<br>";
	$g_lead = ($g_lead / 2) - 25;
	$output .= "<div class='grade'><div class='marker' style='margin-left: {$g_lead}px'></div></div><br>";

	return $output;
}

function stats_grade()
{
	$output = "";

	$table   = "v_route";
	$columns = array ("id", "grade", "grade_seq", "climb_type");
	$where   = NULL;
	$order   = "grade_seq";

	$grade_list = db_select($table, $columns, $where, $order);

	$output .= "<h2>Stats - Grade</h2>";

	$output .= stats_grade_mean ($grade_list);
	$output .= "<br>";
	$output .= "<div class='graph'>";
	$output .= "<img src='img/grade.png'>";
	$output .= "</div>";
	$output .= "<br>";
	$output .= stats_grade_table ($grade_list, FALSE);

	return $output;
}

function stats_main()
{
	$type = get_url_variable('type');

	$last_update = date ("j M Y", strtotime (db_get_last_update()));

	$output  = "<body>";
	$output .= "<div class='header'>Craggy Routes <span>(Last updated: $last_update)</span></div>\n";
	$output .= html_menu();
	$output .= "<div class='content'>\n";
	$output .= stats_grade();
	$output .= "</div>";
	$output .= get_errors();
	$output .= "</body>";
	$output .= "</html>";

	$header  = html_header ("Grades");

	return $header . $output;
}


date_default_timezone_set("UTC");

echo stats_main();
