<?php

// Transfer the climb data from the route table to the climbs table

set_include_path ('/home/flatcap/downloads/update/www');

include 'db.php';

/* CLIMBS
 *	id
 *	climber_id
 *	route_id
 *	date_climbed
 *	success
 *	downclimb
 *	nice
 *	difficulty
 *	notes
 *
 * ROUTE
 *	id			X
 *	panel
 *	colour
 *	grade
 *	notes
 *	setter
 *	date_set
 *	date_climbed		X
 *	success			X
 *	downclimb		X
 *	nice			X
 *	onsight			X
 *	difficulty		X
 *	climb_notes		X
 */

$routes = db_select("route");

foreach ($routes as $id => $row) {
	// insert into climbs
	$climber_id	= 1;
	$route_id	= $row['id'];
	$date_climbed	= $row['date_climbed'];
	$success	= $row['success'];
	$downclimb	= $row['downclimb'];
	$nice		= $row['nice'];
	$onsight	= $row['onsight'];
	$difficulty	= $row['difficulty'];
	$climb_notes	= $row['climb_notes'];

	if ($date_climbed == "0000-00-00")
		continue;

	$sql  = "INSERT INTO climbs (climber_id,route_id,date_climbed,success,downclimb,nice,onsight,difficulty,notes) ";
	$sql .= "VALUES ('{$climber_id}','{$route_id}','{$date_climbed}','{$success}','{$downclimb}','{$nice}','{$onsight}','{$difficulty}','{$climb_notes}')";

	$result = mysql_query($sql);
	if ($result) {
		$id = mysql_insert_id();
	} else {
		printf ("error for: $sql\n");
		break;
	}
}

