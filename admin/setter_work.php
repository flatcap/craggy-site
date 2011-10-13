<?php

date_default_timezone_set('Europe/London');

set_include_path ('../libs');

include_once 'db.php';
include_once 'utils.php';

include 'db_names.php';

//$fh = null;

function xml_error()
{
	// render error info in xml
}

function db_delete($table, $join_tables, $where)
{
	//global $fh;

	if (empty ($where))
		return false;

	$db = db_get_database();

	$query = "delete $table from $join_tables where $where";
	//fwrite ($fh, "$query\n");

	$result = mysql_query($query);

	return mysql_affected_rows();
}


function setter_delete_query ($data)
{
	// VALIDATE USER DATA
	// $data
	$id_list = explode (',', $data);

	// How many setters will be deleted?
	$setter_count = count ($id_list);

	// How many routes will be deleted?
	$table  = $DB_ROUTE;
	$column = 'setter_id';
	$where  = "setter_id in ($data)";
	$route_count = db_count ($table, $column, $where);

	// How many climbs will be deleted?
	$table  = "$DB_SETTER, $DB_ROUTE, $DB_CLIMB";
	$column = "$DB_SETTER.id";
	$where  = "($DB_ROUTE.setter_id = $DB_SETTER.id) and " .
		  "($DB_CLIMB.route_id = $DB_ROUTE.id) and " .
		  "($DB_SETTER.id in ($data))";
	$climb_count = db_count ($table, $column, $where);

	// <result type='setter' action='delete_query'>
	//     <setter>4</setter>
	//     <route>75</route>
	//     <climb>1900</climb>
	// </result>
	return sprintf ("Delete:\n\t%d setters,\n\t%d routes,\n\t%d climbs?", $setter_count, $route_count, $climb_count);
}

function setter_delete ($data)
{
	// VALIDATE USER DATA
	// $data
	$id_list = explode (',', $data);

	// How many climbs will be deleted?
	$table      = $DB_CLIMB;
	$join_table = "$DB_CLIMB, $DB_ROUTE, $DB_SETTER";
	$where = "($DB_CLIMB.route_id = $DB_ROUTE.id) and ($DB_ROUTE.setter_id = $DB_SETTER.id) and ($DB_SETTER.id in ($data))";
	$climb_count = db_delete ($table, $join_table, $where);

	// How many routes will be deleted?
	$table = '';
	$join_table = $DB_ROUTE;
	$where = "setter_id in ($data)";
	$route_count = db_delete ($table, $join_table, $where);

	// How many setters will be deleted?
	$table = '';
	$join_table = $DB_SETTER;
	$where = "id in ($data)";
	$setter_count = db_delete ($table, $join_table, $where);

	// <result type='setter' action='delete_query'>
	//     <setter>4</setter>
	//     <route>75</route>
	//     <climb>1900</climb>
	// </result>
	return sprintf ("DELETED:\n\t%d setters,\n\t%d routes,\n\t%d climbs.", $setter_count, $route_count, $climb_count);
}

function setter_list()
{
//	<setter>
//		<id>23</id>
//		<name>Ruth</name>
//		<count>4</count>
//	</setter>

	$table   = $DB_SETTER .
			" left join $DB_ROUTE on (setter_id=$DB_SETTER.id)";

	$columns = array ("$DB_SETTER.id as id",
			"$DB_SETTER.initials as initials",
			"$DB_SETTER.first_name as first_name",
			"$DB_SETTER.surname as surname",
			"count($DB_ROUTE.id) as count");

	$where   = null;
	$order   = 'id';
	$group   = 'id';

	$list = db_select ($table, $columns, $where, $order, $group);

	$columns = array ('id', 'initials', 'first_name', 'surname', 'count');

	return list_render_xml ('setter', $list, $columns);
}

function setter_main()
{
	global $_GET;
	//global $fh;

	if (!isset ($_GET)) {
		echo 'NO GET';
		return;
	}

	if (!array_key_exists ('action', $_GET)) {
		echo 'NO ACTION';
		return;
	}
	$action = $_GET['action'];

	if (array_key_exists ('data', $_GET)) {
		$data = $_GET['data'];
	} else {
		$data = '';
	}

	//$fh = fopen ('/tmp/db_log', 'a');
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


header('Pragma: no-cache');

echo setter_main();
