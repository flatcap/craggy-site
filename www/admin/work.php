<?php

date_default_timezone_set("UTC");

set_include_path ("../../libs");

include "db.php";
include "utils.php";

function xml_error()
{
	// render error info in xml
}

function list_render_xml ($object_name, &$list, &$columns)
{
	$output = "<{$object_name}_list>\n";

	// foreach row of list
	foreach ($list as $row) {
		$id = $row['id'];
		$output .= "\t<$object_name>\n";

		// foreach col of columns
		foreach ($columns as $col) {
			$output .= sprintf ("\t\t<%s>%s</%s>\n", $col, $row[$col], $col);
		}

		$output .= "\t</$object_name>\n";
	}

	$output .= "</{$object_name}_list>\n";

	return $output;
}

function db_select2 ($table, $columns = NULL, $where = NULL, $order = NULL, $group = NULL)
{
	if (isset($columns)) {
		if (is_array($columns))
			$cols = implode ($columns, ",");
		else
			$cols = $columns;

		$key = $columns[0];
		$key = "id";
	} else {
		$cols = "*";
		$key = "id";
	}

	$query = "select {$cols} from {$table}";

	if (isset ($where)) {
		if (is_array ($where))
			$w = implode ($where, " and ");
		else
			$w = $where;
		$query .= " where " . $w;
	}

	if (isset ($group)) {
		$query .= " group by " . $group;
	}

	if (isset ($order)) {
		$query .= " order by " . $order;
	}

	$db = db_get_database();

	//echo "$query;<br>";
	$result = mysql_query($query);

	$list = array();
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$list[$row[$key]] = $row;
	}

	mysql_free_result($result);
	return $list;
}


function setter_delete_query ($data)
{
	$id_list = explode (',', $data);

	$route_count = 0;
	$climb_count = 0;

	// How many routes will be deleted?
	$table  = "craggy_route";
	$column = "id";
	foreach ($id_list as $id) {
		$where = "setter_id = $id";
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

	// <setter_delete>
	//     <setter>4</setter>
	//     <route>75</route>
	//     <climb>1900</climb>
	// </setter_delete>
	return sprintf ("Delete:\n\t%d setters,\n\t%d routes,\n\t%d climbs?", count ($id_list), $route_count, $climb_count);
}

function setter_list()
{
//	<setter>
//		<id>23</id>
//		<name>Ruth</name>
//		<count>4</count>
//	</setter>

	$table   = "craggy_setter" .
			" left join craggy_route on (setter_id=craggy_setter.id)";

	$columns = array ("craggy_setter.id as id",
			"craggy_setter.name as name",
			"count(craggy_route.id) as count");

	$where   = NULL;
	$order   = "id";
	$group   = "id";

	$list = db_select2 ($table, $columns, $where, $order, $group);

	$columns = array ('id', 'name', 'count');

	return list_render_xml ("setter", $list, $columns);
}


// action: delete, list, update
if (!isset ($_GET)) {
	echo "NO GET";
	return;
}

if (!array_key_exists ('action', $_GET)) {
	echo "NO ACTION";
	return;
}

if (array_key_exists ('data', $_GET)) {
	$data = $_GET['data'];
}

$action = $_GET['action'];
if ($action == 'list') {
	header('Content-Type: application/xml; charset=ISO-8859-1');
	$response = setter_list();
} else if ($action = "delete_query") {
	$response = setter_delete_query($data);
}

echo $response;
