<?php

set_include_path (".:../../www");

include "db.php";
include "utils.php";

if (!isset ($_GET))
	return;
	
if (!array_key_exists ('q', $_GET))
	return;

$q=trim($_GET["q"]);
if ($q != "climb")
	return;

$g_routes  = db_select("route");
$g_colours = db_select("colour");
$g_panels  = db_select("panel");

$table   = "climbs";
$columns = array ("id", "route_id", "date_climbed", "success", "downclimb", "nice", "onsight", "difficulty", "notes");
$where   = array ("climber_id = 1"); 
$order   = NULL;
$climbs = db_select($table, $columns, $where, $order);

$num_climbs = count ($climbs);
if ($num_climbs == 0)
	return;

$count = 0;
$output = "";
$output = "<table border=1 cellspacing=0><tr>";
foreach ($columns as $name) {
	$output .= "<th>{$name}</th>";
}
$output .= "</tr>";

foreach ($climbs as $c) {
	$output .= "<tr>";
	foreach ($c as $name) {
		if (empty ($name))
			$name = "&nbsp;";
		$output .= "<td>{$name}</td>";
	}
	$output .= "</tr>";
	$count++;
	if ($count > 30)
		break;
}

printf ("%d climbs", $num_climbs);
echo $output;
