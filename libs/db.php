<?php

function db_get_database()
{
	include 'conf.php';

	$db = mysql_connect($db_host, $db_user, $db_pass);
	if (!$db) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db($db_database);
	return $db;
}

function db_select ($table, $columns = NULL, $where = NULL, $order = NULL, $group = NULL)
{
	if (isset($columns)) {
		if (is_array($columns))
			$cols = implode ($columns, ',');
		else
			$cols = $columns;

		$key = $columns[0];
		$key = 'id';
	} else {
		$cols = '*';
		$key = 'id';
	}

	$query = "select {$cols} from {$table}";

	if (isset ($where)) {
		if (is_array ($where))
			$w = implode ($where, ' and ');
		else
			$w = $where;
		$query .= ' where ' . $w;
	}

	if (isset ($group)) {
		$query .= ' group by ' . $group;
	}

	if (isset ($order)) {
		$query .= ' order by ' . $order;
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

function db_get_routes()
{
	$db = db_get_database();

	$table_climb_type = db_select('climb_type');
	$table_colour     = db_select('colour');
	$table_grade      = db_select('grade');
	$table_panel      = db_select('panel');
	$table_setter     = db_select('setter');
	$table_route      = db_select('route');

	$routes = array();
	foreach ($table_route as $row) {
		$route = array();

		$route['panel']    = &$table_panel[$row['panel']];
		$route['type']     = &$table_climb_type[$table_panel[$row['panel']]['type']];
		$route['colour']   = &$table_colour[$row['colour']];
		$route['grade']    = &$table_grade[$row['grade']];
		$route['setter']   = &$table_setter[$row['setter']];
		$route['notes']    = $row['notes'];
		$route['date_set'] = $row['date_set'];

		array_push ($routes, $route);
	}

	mysql_close($db);

	return $routes;
}

function db_date($date)
{
	$d = strtotime($date);
	if ($d !== FALSE)
		$result = strftime('%Y/%m/%d', $d);
	else
		$result = '';

	return $result;
}

function db_route_add($routes)
{
	foreach ($routes as $key => $r) {
		// check for both 'valid' and 'set'
		$panel    = parse_panel ($r['panel'], 'id');
		$colour   = parse_colour ($r['colour'], 'id');
		$grade    = parse_grade ($r['grade'], 'id');
		$setter   = parse_setter ($r['setter'], 'id');
		$notes    = $r['notes'];
		$date_set = db_date ($r['date_set']);

		$sql  = 'INSERT INTO route (panel,colour,grade,notes,setter,date_set) ';
		$sql .= "VALUES ('$panel','$colour','$grade','$notes','$setter','$date_set')";
		$result = mysql_query($sql) or die('query failed: ' . mysql_error());

		if ($result) {
			$id = mysql_insert_id();
		} else {
			$id = 0;
		}
	}
}

function db_route_add2($routes)
{
	foreach ($routes as $r) {
		$panel        = $r['panel'];
		$colour       = $r['colour'];
		$grade        = $r['grade'];
		$setter       = $r['setter'];
		$notes        = mysql_real_escape_string ($r['notes']);
		$date_set     = $r['date_set'];

		$sql  = 'INSERT INTO route (panel,colour,grade,notes,setter,date_set) ';
		$sql .= "VALUES ('$panel','$colour','$grade','$notes','$setter','$date_set')";
		$result = mysql_query($sql) or die('query failed: ' . mysql_error());

		if ($result) {
			$id = mysql_insert_id();
		} else {
			$id = 0;
		}
	}
}

function db_route_delete($where)
{
	if (empty ($where))
		return FALSE;

	$query = 'delete from route where ' . $where;

	$db = db_get_database();

	$result = mysql_query($query);

	return $result;
}

function db_get_last_update()
{
	include 'dbnames.php';

	$db = db_get_database();

	$query = "select value from $DB_DATA where name = 'last_update'";

	$result = mysql_query($query);

	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	mysql_free_result($result);

	return $row['value'];
}

function db_set_last_update($date = '')
{
	$db = db_get_database();

	if (empty ($date))
		$date = date ('Y-m-d');

	$query  = 'update data set ';
	$query .= "data.value='$date' ";
	$query .= "where name='last_update';";

	//var_dump ($query);
	$result = mysql_query($query);
	//var_dump ($result);

	return $result;
}

function db_truncate_route()
{
	$db = db_get_database();

	$query = 'truncate route;';

	return mysql_query($query);
}

function db_count($table, $column, $where = NULL)
{
	$query = "select count({$column}) as total from {$table}";

	if (isset ($where)) {
		if (is_array ($where))
			$w = implode ($where, ' and ');
		else
			$w = $where;
		$query .= ' where ' . $w;
	}

	$db = db_get_database();

	$result = mysql_query($query);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	mysql_free_result($result);

	return $row['total'];
}

