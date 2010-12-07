<?php

set_include_path (".:../../www");

include "db.php";

if (!isset ($_GET))
	return;
	
if (!array_key_exists ('q', $_GET))
	return;

$q=trim($_GET["q"]);

header('Content-Type: application/xml; charset=ISO-8859-1');

$table   = "setter";
$columns = array ("id", "initials", "name");
$where   = array ("initials like '{$q}%' or name like '{$q}%'"); 
$order   = NULL;

$setter_list = db_select($table, $columns, $where, $order);

$output = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>\n";
$output .= "<setters>\n";

foreach ($setter_list as $setter) {
	$output .= "\t<setter>\n";
	foreach ($columns as $name) {
		$value = $setter[$name];
		$output .= "\t\t<$name>$value</$name>\n";
	}
	$output .= "\t</setter>\n";
}

$output .= "</setters>\n";

echo $output;
