<?php

set_include_path ('../../libs');

include 'db.php';
include 'db_names.php';

if (!isset ($_GET))
	return;

if (!array_key_exists ('q', $_GET))
	return;

$q = trim($_GET['q']);

header('Content-Type: application/xml; charset=ISO-8859-1');

$table   = $DB_SETTER;
$columns = array ('id', 'initials', 'first_name', 'surname');
$where   = array ("initials like '%{$q}%' or first_name like '%{$q}%' or surname like '%{$q}%'");
$order   = null;

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
