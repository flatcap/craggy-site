<?php

date_default_timezone_set("UTC");

set_include_path ("../../libs");

include "db.php";
include "utils.php";

//$fh = NULL;

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

function db_delete($table, $join_tables, $where)
{
	//global $fh;

	if (empty ($where))
		return FALSE;

	$db = db_get_database();

	$query = "delete $table from $join_tables where $where";
	//fwrite ($fh, "$query\n");

	$result = mysql_query($query);

	return mysql_affected_rows();
}


function setter_delete_query ($data)
{
	$id_list = explode (',', $data);

	$route_count  = 0;
	$climb_count  = 0;
	$setter_count = 0;

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

	// How many setters will be deleted?
	$setter_count = count ($id_list);

	// <setter_delete>
	//     <setter>4</setter>
	//     <route>75</route>
	//     <climb>1900</climb>
	// </setter_delete>
	return sprintf ("Delete:\n\t%d setters,\n\t%d routes,\n\t%d climbs?", $setter_count, $route_count, $climb_count);
}

function setter_delete ($data)
{
	$id_list = explode (',', $data);

	$climb_count  = 0;
	$route_count  = 0;
	$setter_count = 0;

	// How many climbs will be deleted?
	$table      = "craggy_climb";
	$join_table = "craggy_climb, craggy_route, craggy_setter";
	foreach ($id_list as $id) {
		$where = "(craggy_climb.route_id = craggy_route.id) and (craggy_route.setter_id = craggy_setter.id) and (craggy_setter.id = $id)";
		$climb_count += db_delete ($table, $join_table, $where);
	}

	// How many routes will be deleted?
	$table = "";
	$join_table = "craggy_route";
	foreach ($id_list as $id) {
		$where = "setter_id = $id";
		$route_count += db_delete ($table, $join_table, $where);
	}

	// How many setters will be deleted?
	$table = "";
	$join_table = "craggy_setter";
	foreach ($id_list as $id) {
		$where = "id = $id";
		$setter_count += db_delete ($table, $join_table, $where);
	}

	// <setter_delete>
	//     <setter>4</setter>
	//     <route>75</route>
	//     <climb>1900</climb>
	// </setter_delete>
	return sprintf ("DELETED:\n\t%d setters,\n\t%d routes,\n\t%d climbs.", $setter_count, $route_count, $climb_count);
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

function setter_main()
{
	global $_GET;
	//global $fh;

	if (!isset ($_GET)) {
		echo "NO GET";
		return;
	}

	if (!array_key_exists ('action', $_GET)) {
		echo "NO ACTION";
		return;
	}
	$action = $_GET['action'];

	if (array_key_exists ('data', $_GET)) {
		$data = $_GET['data'];
	} else {
		$data = "";
	}

	//$fh = fopen ("/tmp/db_log", "a");
	//fwrite ($fh, "action=$action,data=$data\n");

	// action: delete, list, update
	switch ($action) {
		case 'list':
			header('Content-Type: application/xml; charset=ISO-8859-1');
			$response = setter_list();
			break;
		case 'delete_query':
			$response = setter_delete_query($data);
			break;
		case 'delete':
			$response = setter_delete($data);
			break;
	}

	//fclose ($fh);

	return $response;
}


echo setter_main();
