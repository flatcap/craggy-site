<?php

function db_get_database()
{
	include 'conf.php';

	$db = new mysqli($db_host, $db_user, $db_pass, $db_database);
	if ($db->connect_error) {
		die('Connect Error (' . $db->connect_errno . ') ' . $db->connect_error);
	}

	//echo "db = $db<br>";
	return $db;
}

function db_select ($db, $table, $columns = null, $where = null, $order = null, $group = null)
{
	//echo "db = $db<br>";
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

	//echo "$query;<br>";
	$result = $db->query($query);

	$list = array();
	if ($result) {
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$list[$row[$key]] = $row;
		}
	}

	$result->close();
	return $list;
}

function db_select2 ($db, $table, $columns = null, $where = null, $order = null, $group = null)
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

	//echo "$query;<br>";
	$result = $db->query($query);

	$list = array();
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$list[] = $row;
	}

	$result->close();
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

function db_route_delete ($db, $ids, $date)
{
	if (count ($ids) == 0)
		return null;

	$retval = array();

	$id_list = implode (',', $ids);

	$query = "update route set date_end = '$date' where id in ($id_list)";	// date needs to be passed in
	$result = $db->query($query);
	if ($result === true) {
		$retval['routes'] = $db->affected_rows();
	} else {
		$retval['routes'] = -1;
	}

	return $retval;
}

function db_get_last_update($db)
{
	include 'db_names.php';

	$query = "select value from $DB_DATA where name = 'last_update'";

	$result = $db->query($query);

	$row = $result->fetch_array(MYSQLI_ASSOC);
	$result->close();

	return $row['value'];
}

function db_set_last_update($db, $date = '')
{
	if (empty ($date))
		$date = date ('Y-m-d');

	$query  = 'update data set ';
	$query .= "data.value='$date' ";
	$query .= "where name='last_update';";

	//var_dump ($query);
	$result = $db->query($query);
	//var_dump ($result);

	return $result;
}

function db_get_data ($db, $name)
{
	include 'db_names.php';

	$query = "select value from $DB_DATA where name = '$name'";

	$result = $db->query($query);
	if (!$result) {
		return false;
	}

	$row = $result->fetch_array(MYSQLI_ASSOC);
	$result->close();

	return $row['value'];
}

function db_count($db, $table, $column, $where = null)
{
	$query = "select count({$column}) as total from {$table}";

	if (isset ($where)) {
		if (is_array ($where))
			$w = implode ($where, ' and ');
		else
			$w = $where;
		$query .= ' where ' . $w;
	}

	$result = $db->query($query);
	$row = $result->fetch_array(MYSQLI_ASSOC);
	$result->close();

	return $row['total'];
}

