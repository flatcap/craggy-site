<?php

include "db.php";
include "utils.php";

function stats_age()
{
	$output = "";

	$table   = "v_route";
	$columns = array("id", "date_set");

	$list = db_select($table, $columns);

	$totals = array();
	for ($i = -1; $i < 8; $i++) {
		$totals[$i] = 0;
	}

	$today = strtotime ("today");
	foreach ($list as $row) {
		$date = $row['date_set'];
		if (empty ($date) || ($date == "0000-00-00"))
			$age = -1;
		else
			$age = floor (($today - strtotime ($row['date_set'])) / 2635200);

		if ($age > 7)
			$age = 7;

		$totals[$age]++;
	}

	$output .= "<h2>Stats - Age</h2>";
	$output .= "<img src='img/age.png'>";
	$output .= "<table border='1' cellpadding='3' cellspacing='0'>";
	$output .= "<tr>";
	$output .= "<th>Age</th>";
	$output .= "<th>Count</th>";
	$output .= "<th>Total</th>";
	$output .= "</tr>";

	$total = 0;
	foreach ($totals as $age => $count) {
		if ($age < 0)
			$age = "N/A";
		$total += $count;
		$output .= "<tr>";
		$output .= "<td>$age</td>";
		$output .= "<td>$count</td>";
		$output .= "<td>$total</td>";
		$output .= "</tr>";
	}
	$output .= "</table>";

	return $output;
}

function stats_colour()
{
	$output = "";

	$table   = "v_route";
	$columns = array("id", "colour");
	$order   = "colour";

	$list = db_select($table, $columns, NULL, $order);

	$totals = array();
	foreach ($list as $row) {
		$c = $row['colour'];
		if (array_key_exists ($c, $totals))
			$totals[$c]++;
		else
			$totals[$c] = 1;
	}

	$output .= "<h2>Stats - Colour</h2>";
	$output .= "<img src='img/colour.png'>";
	$output .= "<table border='1' cellpadding='3' cellspacing='0'>";
	$output .= "<tr>";
	$output .= "<th>Colour</th>";
	$output .= "<th>Count</th>";
	$output .= "<th>Total</th>";
	$output .= "</tr>";

	$total = 0;
	foreach ($totals as $colour => $count) {
		$total += $count;
		$output .= "<tr>";
		$output .= "<td>$colour</td>";
		$output .= "<td>$count</td>";
		$output .= "<td>$total</td>";
		$output .= "</tr>";
	}
	$output .= "</table>";

	return $output;
}

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
		$gnum = intval ($row['grade_num'] / $divisor);

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

function stats_grade_get_labels ($grades, $g_mean)
{
	$low = "";
	$high = "";

	foreach ($grades as $g) {
		$num = $g['order'];

		if ($g_mean > $num) {
			$low = $g['grade'];
			continue;
		} else if ($g_mean == $num) {
			return $g['grade'];
		} else {
			$high = $g['grade'];
			break;
		}
	}

	return "$low/$high";
}

function stats_grade_mean ($grade_list)
{
	$output = "";

	$g_both = 0; $c_both = 0;
	$g_lead = 0; $c_lead = 0;
	$g_topr = 0; $c_topr = 0;

	foreach ($grade_list as $route) {

		$g_num = $route['grade_num'];

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
	//$label = stats_grade_get_labels ($grades, $g_both);
	$output .= "<b>All Routes</b>:<br>";
	$g_both = ($g_both / 2) - 25;
	$output .= "<div class='grade'><div class='marker' style='margin-left: {$g_both}px'></div></div><br>";

	//$label = stats_grade_get_labels ($grades, $g_topr);
	$output .= "<b>Top Ropes</b>:<br>";
	$g_topr = ($g_topr / 2) - 25;
	$output .= "<div class='grade'><div class='marker' style='margin-left: {$g_topr}px'></div></div><br>";

	//$label = stats_grade_get_labels ($grades, $g_lead);
	$output .= "<b>Lead Routes</b>:<br>";
	$g_lead = ($g_lead / 2) - 25;
	$output .= "<div class='grade'><div class='marker' style='margin-left: {$g_lead}px'></div></div><br>";

	return $output;
}

function stats_grade()
{
	$output = "";

	$table   = "v_route";
	$columns = array ("id", "grade", "grade_num", "climb_type");
	$where   = NULL;
	$order   = "grade_num";

	$grade_list = db_select($table, $columns, $where, $order);

	$output .= "<h2>Stats - Grade</h2>";

	$output .= stats_grade_mean ($grade_list);
	$output .= "<br>";
	//$output .= stats_grade_table ($grade_list, TRUE);
	//$output .= "<br>";
	$output .= "<div class='graph'>";
	//$output .= "<h2 style='text-align: center'>Spread of Grades</h2>";
	//$output .= "<img src='img/graph_grades.png'>";
	$output .= "<img src='img/grade.png'>";
	$output .= "</div>";
	$output .= "<br>";
	$output .= stats_grade_table ($grade_list, FALSE);

	return $output;
}

function stats_setters()
{
	$output = "";

	$table   = "v_route";
	$columns = array("id", "setter");
	$where   = NULL;
	$order   = "setter";

	$list = db_select($table, $columns, $where, $order);

	$setters = array();
	foreach ($list as $s) {
		$name = $s['setter'];
		if (empty ($name))
			$name = "N/A";
		if (array_key_exists ($name, $setters))
			$setters[$name]++;
		else
			$setters[$name] = 1;
	}

	$output .= "<h2>Stats - Setters</h2>";
	$output .= "<table border='1' cellpadding='3' cellspacing='0'>";
	$output .= "<tr>";
	$output .= "<th>Setter</th>";
	$output .= "<th>Count</th>";
	$output .= "</tr>";

	foreach ($setters as $name => $count) {
		$output .= "<tr><td>$name</td><td>$count</td></tr>";
	}

	$output .= "</table>";

	return $output;
}

function stats_style()
{
	$output = "";

	$table   = "craggy_panel";
	$columns = array("id", "tags");
	$where   = NULL;
	$order   = NULL;

	$list = db_select($table, $columns, $where, $order);

	$tag_list = array();
	foreach ($list as $row) {
		$tags = explode (',', $row['tags']);
		foreach ($tags as $t) {
			if (array_key_exists ($t, $tag_list))
				$tag_list[$t]++;
			else
				$tag_list[$t] = 1;
		}
	}

	$output .= "<h2>Stats - Styles</h2>";
	$output .= "<table border='1' cellpadding='3' cellspacing='0'>";
	$output .= "<thead>";
	$output .= "<tr>";
	$output .= "<th>Style</th>";
	$output .= "<th>Count</th>";
	$output .= "</tr>";
	$output .= "</thead>";
	$output .= "<tbody>";

	ksort ($tag_list);
	foreach ($tag_list as $tag => $count) {
		$output .= "<tr><td>" . ucfirst($tag) . "</td><td>{$count}</td></tr>";
	}

	$output .= "</tbody>";
	$output .= "</table>";
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

	switch ($type) {
		case "colour":  $header = "Colour"; $output .= stats_colour();  break;
		case "grades":  $header = "Grade";  $output .= stats_grade();   break;
		case "age":     $header = "Age";    $output .= stats_age();     break;
		case "style":   $header = "Style";  $output .= stats_style();   break;
		case "setters": $header = "Setter"; $output .= stats_setters(); break;
		default:        $header = "Unknown"; $output .= "Unknown URL";   break;
	}

	$output .= "</div>";
	$output .= get_errors();
	$output .= "</body>";
	$output .= "</html>";

	$header  = html_header ($header);

	return $header . $output;
}


date_default_timezone_set("UTC");

echo stats_main();

