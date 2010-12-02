<?php

set_include_path (".:..");

include "db.php";
include "utils.php";

$g_col_sort  = array (
	"colour"   => "colour",
	"grade"    => "grade",
	"panel"    => "panel",
	"priority" => "priority",
	"score"    => "score",
	"type"     => "type"
);

function cmp_panel2($a, $b)
{
	$p1 = $a['panel'];
	$g1 = $a['grade_num'];
	$c1 = $a['colour'];
	$x1 = $a['priority'];

	$p2 = $b['panel'];
	$g2 = $b['grade_num'];
	$c2 = $b['colour'];
	$x2 = $b['priority'];

	if ($p1 != $p2)
		return ($p1 < $p2) ? -1 : 1;

	if ($g1 != $g2)
		return ($g1 < $g2) ? -1 : 1;

	if ($c1 != $c2)
		return ($c1 < $c2) ? -1 : 1;

	return ($x1 < $x2) ? -1 : 1;
}

function cmp_colour($a, $b)
{
	$c1 = $a['colour'];
	$p1 = $a['panel'];
	$g1 = $a['grade_num'];

	$c2 = $b['colour'];
	$p2 = $b['panel'];
	$g2 = $b['grade_num'];

	if ($c1 != $c2)
		return ($c1 < $c2) ? -1 : 1;

	if ($p1 != $p2)
		return ($p1 < $p2) ? -1 : 1;

	return ($g1 < $g2) ? -1 : 1;
}

function cmp_priority($a, $b)
{
	$x1 = $a['priority'];
	$p1 = $a['panel'];
	$g1 = $a['grade_num'];
	$c1 = $a['colour'];

	$x2 = $b['priority'];
	$p2 = $b['panel'];
	$g2 = $b['grade_num'];
	$c2 = $b['colour'];

	if ($x1 != $x2)
		return ($x1 < $x2) ? -1 : 1;

	if ($p1 != $p2)
		return ($p1 < $p2) ? -1 : 1;

	if ($g1 != $g2)
		return ($g1 < $g2) ? -1 : 1;

	return ($c1 < $c2) ? -1 : 1;
}

function cmp_score($a, $b)
{
	$s1 = $a['score'];
	$p1 = $a['panel'];
	$g1 = $a['grade_num'];
	$c1 = $a['colour'];

	$s2 = $b['score'];
	$p2 = $b['panel'];
	$g2 = $b['grade_num'];
	$c2 = $b['colour'];

	if ($s1 != $s2)
		return ($s1 > $s2) ? -1 : 1;

	if ($p1 != $p2)
		return ($p1 < $p2) ? -1 : 1;

	if ($g1 != $g2)
		return ($g1 < $g2) ? -1 : 1;

	return ($c1 < $c2) ? -1 : 1;
}

function cmp_type($a, $b)
{
	$t1 = $a['climb_type'];
	$p1 = $a['panel'];
	$g1 = $a['grade_num'];
	$c1 = $a['colour'];

	$t2 = $b['climb_type'];
	$p2 = $b['panel'];
	$g2 = $b['grade_num'];
	$c2 = $b['colour'];

	if ($t1 != $t2)
		return ($t1 < $t2) ? -1 : 1;

	if ($p1 != $p2)
		return ($p1 < $p2) ? -1 : 1;

	if ($g1 != $g2)
		return ($g1 < $g2) ? -1 : 1;

	return ($c1 < $c2) ? -1 : 1;
}


function work_priority(&$list, $pri)
{
	foreach ($list as $index => $row) {
		$list[$index]['priority'] = $pri;
	}
}

function work_todo()
{
	$climber_id = 1;
	$table   = "route left join climbs on ((climbs.route_id = route.id) and (climber_id = {$climber_id})) left join colour on (route.colour = colour.id) left join panel on (route.panel = panel.id) left join grade on (route.grade = grade.id) left join v_panel on (route.panel = v_panel.number)";
	$columns = array ("route.id as id", "panel.number as panel", "colour.colour as colour", "grade.grade as grade", "grade.order as grade_num", "climber_id", "date_climbed", "v_panel.climb_type as climb_type", "success", "nice as n", "onsight as o", "difficulty as diff", "climbs.notes as notes");
	$where   = array ("((success < 3) OR (success is NULL))", "grade.order < 600");

	$list = db_select($table, $columns, $where);

	work_priority ($list, "T");
	return $list;
}

function work_downclimb()
{
	$climber_id = 1;
	$table   = "route left join climbs on ((climbs.route_id = route.id) and (climber_id = {$climber_id})) left join colour on (route.colour = colour.id) left join panel on (route.panel = panel.id) left join grade on (route.grade = grade.id) left join v_panel on (route.panel = v_panel.number)";
	$columns = array ("route.id as id", "panel.number as panel", "colour.colour as colour", "grade.grade as grade", "grade.order as grade_num", "climber_id", "date_climbed", "v_panel.climb_type as climb_type", "success", "nice as n", "onsight as o", "difficulty as diff", "climbs.notes as notes");
	$where   = array ("success <> 4", "grade.order < 400");

	$list = db_select($table, $columns, $where);

	work_priority ($list, "D");
	return $list;
}

function work_seldom_range ($m_start, $m_finish)
{
	$when_start  = db_date ("$m_start months ago");

	$climber_id = 1;
	$table   = "route left join climbs on ((climbs.route_id = route.id) and (climber_id = {$climber_id})) left join colour on (route.colour = colour.id) left join panel on (route.panel = panel.id) left join grade on (route.grade = grade.id) left join v_panel on (route.panel = v_panel.number)";
	$columns = array ("route.id as id", "panel.number as panel", "colour.colour as colour", "grade.grade as grade", "grade.order as grade_num", "climber_id", "date_climbed", "v_panel.climb_type as climb_type", "success", "nice as n", "onsight as o", "difficulty as diff", "climbs.notes as notes");
	$where   = array ("grade.order < 600", "date_climbed < '$when_start'");

	if (isset ($m_finish)) {
		$when_finish = db_date ("$m_finish months ago");
		array_push ($where, "date_climbed > '$when_finish'");
	}

	$list = db_select($table, $columns, $where);

	return $list;
}

function work_seldom()
{
	$output = array();
	$ranges = array (6, 4, 3, 2);

	$start  = NULL;
	$finish = NULL;
	foreach ($ranges as $num) {
		$finish = $start;
		$start  = $num;

		$list = work_seldom_range ($start, $finish);
		work_priority ($list, $start);

		$output = array_merge ($output, $list);
	}

	return $output;
}

function work_score (&$list)
{
	$total = 0;
	$scores = array();

	foreach ($list as $row) {
		$p = $row['panel'];
		switch ($row['priority']) {
			case "2": $score = 1; break;
			case "3": $score = 2; break;
			case "4": $score = 4; break;
			case "D": $score = 6; break;
			case "T": $score = 8; break;
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

function work_flatten ($list)
{
	$output = array();

	$old = NULL;
	foreach ($list as $row) {
		$new = $row['panel'] . $row['colour'] . $row['grade'];
		if ($new != $old) {
			$output[] = $row;
			$old = $new;
		}
	}

	return $output;
}


function work_main ($options)
{
	$list_todo = work_todo();
	$list_down = work_downclimb();
	$list_seld = work_seldom();

	$all = array_merge ($list_todo, $list_down, $list_seld);
	usort ($all, "cmp_panel2");
	$all = work_flatten ($all);
	$score = work_score ($all);

	switch ($options["sort"]) {
		case "colour":   $cmp = "cmp_colour";   break;
		case "grade":    $cmp = "cmp_grade";    break;
		case "priority": $cmp = "cmp_priority"; break;
		case "score":    $cmp = "cmp_score";    break;
		case "type":     $cmp = "cmp_type";     break;
		default:         $cmp = "cmp_panel2";   break;
	}
	usort ($all, $cmp);

	process_type ($all);
	$columns = array ("panel", "colour", "grade", "climb_type", "priority", "score");
	$widths = column_widths ($all, $columns, TRUE);
	fix_justification ($widths);

	$count  = count ($all);
	$output = "";
	//header("Pragma: no-cache");
	switch ($options["format"]) {
		case "html":
			$last_update = date ("j M Y", strtotime (db_get_last_update()));

			$output .= html_header ("Work", "../");
			$output .= "<body>";

			$output .= "<div class='download'>";
			$output .= "<h1>Route Data</h1>";
			$output .= "<a href='?format=text'><img src='../img/txt.png'></a>";
			$output .= "&nbsp;&nbsp;";
			$output .= "<a href='?format=csv'><img src='../img/ss.png'></a>";
			$output .= "</div>";

			$output .= "<div class='header'>Work <span>(Last updated: $last_update)</span></div>\n";
			$output .= html_menu("../");
			$output .= "<div class='content'>\n";
			$output .= "<h2>Work <span>($count climbs)</span><span> (Score = $score)</span></h2>\n";
			$output .= list_render_html ($all, $columns, $widths);
			$output .= "</div>";
			$output .= get_errors();
			$output .= "</body>";
			$output .= "</html>";
			break;

		case "csv":
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="work.csv"');
			$output .= list_render_csv ($all, $columns);
			break;

		case "text":
		default:
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="work.txt"');
			$output .= "Work ($count climbs)\r\n";
			$output .= list_render_text ($all, $columns, $widths);
			break;
	}

	return $output;
}

function work_command_line ($format, $def_format, $sort)
{
	$longopts  = array("format:", "sort:");

	$options = getopt(NULL, $longopts);

	if (!array_key_exists ("format", $options) || !in_array ($options["format"], $format)) {
		$options["format"] = $format[$def_format];
	}

	if (!array_key_exists ("sort", $options) || !in_array ($options["sort"], $sort)) {
		$options["sort"] = NULL;
	}

	return $options;
}

function work_browser_options ($format, $def_format, $sort)
{
	$options = array();

	$f = get_url_variable ("format");
	if (!in_array ($f, $format))
		$f = $format[$def_format];

	$s = get_url_variable ("sort");
	if (!in_array ($s, $sort))
		$s = NULL;

	$options["format"] = $f;
	$options["sort"]   = $s;

	return $options;
}


date_default_timezone_set("UTC");

$format = array ("csv", "html", "text");
$sort   = array ("age", "colour", "grade", "panel", "priority", "success", "type", "score");

if (isset ($argc))
	$options = work_command_line ($format, 2, $sort);
else
	$options = work_browser_options ($format, 1, $sort);

echo work_main($options);

