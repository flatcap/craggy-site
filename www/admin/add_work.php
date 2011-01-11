<?php

set_include_path ('../../libs');

include 'db.php';
include 'db_names.php';
include 'utils.php';

function route_add ($data)
{
	//printf ("data = >>$data<<\n");
	// parse:
	//	45 red 5+, gn 6a, bg 6b into
	// into
	//	45 Red 5+
	//	45 Green 6a
	//	45 Beige 6b

	$routes = array();

	$list = explode (' ', $data, 2);

	$panel = $list[0];
	$data  = $list[1];

	$list = explode (',', $data);
	foreach ($list as $item) {
		$item = trim ($item);
		list ($colour, $grade) = explode (' ', $item, 2);
		$colour = trim ($colour);
		$grade = trim ($grade);
		$routes[] = array ('panel' => $panel, 'colour' => $colour, 'grade' => $grade);
	}

	var_dump ($routes);
	return;
}

function route_save ($data)
{
}

function route_main()
{
	global $_GET;

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

	switch ($action) {
		case 'add':
			//header('Content-Type: application/xml; charset=ISO-8859-1');
			$response = route_add($data);
			break;
		case 'save':
			$response = route_save($data);
			break;
		default:
			$response = "unknown action";
			break;
	}

	return $response;
}


if (isset ($argc)) {
	$_GET = array('action' => 'add', 'data' => '45   red  5+,     gn   6a  ,  bg   6b  ');
}

echo route_main();

