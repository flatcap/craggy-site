<?php

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

function db_truncate_route()
{
	$db = db_get_database();

	$query = 'truncate route;';

	return mysql_query($query);
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

function db_route_delete($where)
{
	if (empty ($where))
		return false;

	$query = 'delete from route where ' . $where;

	$db = db_get_database();

	$result = mysql_query($query);

	return mysql_affected_rows();
}

