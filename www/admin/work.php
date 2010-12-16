<?php

set_include_path ("../../libs");

include "db.php";
include "utils.php";

date_default_timezone_set("UTC");

if (isset ($_GET) && array_key_exists ('delete', $_GET))
	$param = $_GET['delete'];
else
	$param = "5";

$id_list = explode (',', $param);

$route_count = 0;
$climb_count = 0;

// How many routes will be deleted?
$table  = "craggy_route";
$column = "id";
foreach ($id_list as $id) {
	$where = "id = $id";
	$route_count += db_count ($table, $column, $where);
}

// How many climbs will be deleted?
$table  = "craggy_setter, craggy_route, craggy_climb";
$column = "craggy_setter.id";
$where  = "craggy_route.setter_id = craggy_setter.id and " .
	  "craggy_climb.route_id = craggy_route.id and ";
foreach ($id_list as $id) {
	$where2 = $where . "craggy_setter.id = $id";
	$climb_count += db_count ($table, $column, $where2);
}

printf ("%d routes will be deleted\n%d climbs will be deleted", $route_count, $climb_count);
