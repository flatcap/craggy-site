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

function db_select ($table, $columns = null, $where = null, $order = null, $group = null)
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

function db_select2 ($table, $columns = null, $where = null, $order = null, $group = null)
{
	if (isset($columns)) {
		if (is_array($columns))
			$cols = implode ($columns, ',');
		else
			$cols = $columns;
	} else {
		$cols = '*';
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
		$list[] = $row;
	}

	mysql_free_result($result);
	return $list;
}

function db_date($date)
{
	$d = strtotime($date);
	if ($d !== false)
		$result = strftime('%Y/%m/%d', $d);
	else
		$result = '';

	return $result;
}

function db_route_delete ($ids, $date)
{
	if (count ($ids) == 0)
		return null;

	$retval = array();

	$db = db_get_database();
	$id_list = implode (',', $ids);

	$query = "update route set date_end = '$date' where id in ($id_list)";	// date needs to be passed in
	$result = mysql_query($query);
	if ($result === true) {
		$retval['routes'] = mysql_affected_rows();
	} else {
		$retval['routes'] = -1;
	}

	return $retval;
}

function db_get_last_update()
{
	include 'db_names.php';

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

function db_get_data ($name)
{
	include 'db_names.php';

	$db = db_get_database();

	$query = "select value from $DB_DATA where name = '$name'";

	$result = mysql_query($query);
	if (!$result) {
		return false;
	}

	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	mysql_free_result($result);

	return $row['value'];
}

function db_count($table, $column, $where = null)
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

