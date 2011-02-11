<?php

set_include_path ('../../libs');

include 'db.php';
include 'db_names.php';
include 'utils.php';
include 'colour.php';

$g_routes  = null;
$g_panels  = null;
$g_colours = null;

if (!isset ($_GET))
	return;

if (!array_key_exists ('q', $_GET))
	return;

$q=$_GET['q'];
$len = strlen ($q);
$last = $q[$len-1];
$trail_space = (($last == ' ') || ($last == ','));
$q=trim ($q);
if (empty ($q))
	return;

$parts = preg_split('/[\s,]+/', $q);
$panel = array_shift ($parts);
foreach ($parts as $key => $p) {
	if ($p == '') {
		unset ($parts[$key]);
	}
}

if (!is_numeric ($panel)) {
	printf ("Not a panel name: '%s'", $panel);
	return;
}

$g_routes  = db_select($DB_ROUTE);
$g_colours = db_select($DB_COLOUR);
$g_panels  = db_select($DB_PANEL);
$g_grades  = db_select($DB_GRADE);

$panel_id = null;
foreach ($g_panels as $id => $p) {
	if ($p['name'] == $panel) {
		$panel_id = $p['id'];
		break;
	}
}

if ($panel_id === null) {
	printf ("Panel '%d' doesn't exist\n", $panel);
	return;
}

$matches = array();
foreach ($g_routes as $id => $r) {
	if ($r['panel_id'] == $panel_id) {
		$col_id   = $r['colour_id'];
		$grade_id = $r['grade_id'];
		$col = $g_colours[$col_id]['colour'];
		$grade = $g_grades[$grade_id]['grade'];
		$r['grade'] = $grade;
		$matches[$col] = $r;
	}
}

$num_routes = count ($matches);
if ($num_routes == 0) {
	printf ("Panel '%d' doesn't have any routes", $panel);
	return;
}

$list = array();
foreach ($matches as $m) {
	$list[] = $g_colours[$m['colour_id']]['colour'] . ' ' . $m['grade'];
}

if ($trail_space) {
	foreach ($parts as &$p) {
		$col = colour_match ($p);
		if ($col !== null) {
			$p = $col['colour'];
		}
	}
}

$c = $panel;
$cols = implode (', ', $parts);
if (!empty ($cols))
	$c .= ' ' . $cols;

if ($trail_space)
	$c .= ' ';
printf ('%s - %d %s', $c, $panel, implode (', ', $list));

