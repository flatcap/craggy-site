<?php

include "db.php";
include "utils.php";
include "mark.php";

function get_setter ($setters)
{
	$inits = array();
	foreach ($setters as $name => $null) {
		if (empty ($name))
			continue;
		$tok = strtok ($name, " ");
		$i = $tok[0];
		$tok = strtok (" ");
		$i .= $tok[0];
		$inits[] = strtolower ($i);
	}

	return implode ($inits, ",");
}

function get_date ($date)
{
	return date ("j/m", strtotime ($date));
}

function boards_main ($options)
{
	$table   = "v_routes";
	$columns = array ("id", "panel", "colour", "grade", "setter", "date_set");
	$where   = NULL;
	$order   = "panel,grade_num,colour";

	$list = db_select($table, $columns, $where, $order);

	//header("Pragma: no-cache");
	$output = html_header ("Boards");
	$boards = array();
	$panel = 0;
	foreach ($list as $row) {
		$p = $row['panel'];
		if ($panel != $p) {
			$panel = $p;
			$boards[$panel] = array("setter" => array(), "date_set" => NULL);
		}

		$boards[$panel]["setter"][$row["setter"]] = NULL;
		$d1 = $row["date_set"];
		$d2 = $boards[$panel]["date_set"];
		if ($d1 > $d2)
			$boards[$panel]["date_set"] = $d1;
		array_push ($boards[$panel], $row);
	}

	// array (0, 1, 2, 3, 4, date_set, setter)

	if ($options["format"] == "html")
		$output .= "<pre>";

	foreach ($boards as $panel => $routes) {
		$output .= sprintf ("%2d | ", $panel);
		for ($i = 0; $i < 5; $i++) {
			if (array_key_exists ($i, $routes))
				$output .= sprintf ("%-12s %-3s | ", $routes[$i]['colour'], $routes[$i]['grade']);
			else
				$output .= "                 | ";
		}
		$s = get_setter ($routes['setter']);
		$d = get_date   ($routes['date_set']);
		$output .= sprintf ("%-5s | %-8s |", $d, $s);
		$output .= "\n";
	}

	if ($options["format"] == "html")
		$output .= "</pre>";

	return $output;
}


function boards_command_line ($format, $def_format)
{
	$longopts  = array("format:");

	$options = getopt(NULL, $longopts);

	if (!array_key_exists ("format", $options) || !in_array ($options["format"], $format)) {
		$options["format"] = $format[$def_format];
	}

	return $options;
}

function boards_browser_options ($format, $def_format)
{
	$options = array();

	$f = get_url_variable ("format");
	if (!in_array ($f, $format))
		$f = $format[$def_format];

	$options["format"] = $f;

	return $options;
}


date_default_timezone_set("UTC");

$format = array ("csv", "html", "text");

if (isset ($argc))
	$options = boards_command_line ($format, 2);
else
	$options = boards_browser_options ($format, 1);

echo boards_main($options);

