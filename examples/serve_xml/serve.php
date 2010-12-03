<?php

set_include_path (".:../../www");

include "db.php";
#include "utils.php";

#if (!isset ($_GET))
#	return;
#	
#if (!array_key_exists ('q', $_GET))
#	return;
#
#$q=trim($_GET["q"]);
#if ($q != "climb")
#	return;

#$g_routes  = db_select("route");
#$g_colours = db_select("colour");
#$g_panels  = db_select("panel");

header('Content-Type: application/xml; charset=ISO-8859-1');

$table   = "climbs";
$columns = array ("id", "route_id", "date_climbed", "success", "nice", "onsight", "difficulty", "notes");
$where   = array ("climber_id = 1"); 
$order   = NULL;

$climb_list = db_select($table, $columns, $where, $order);

$count = 0;
$output = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>\n";
$output .= "<climbs>\n";

foreach ($climb_list as $climb) {
	$output .= "\t<climb>\n";
	foreach ($columns as $name) {
		$value = $climb[$name];
		$output .= "\t\t<$name>$value</$name>\n";
	}
	$output .= "\t</climb>\n";
	$count++;
	if ($count >= 10)
		break;
}

$output .= "</climbs>\n";

echo $output;