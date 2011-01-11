<?php

set_include_path ('../../libs');

include 'db.php';
include 'db_names.php';
include 'utils.php';

function route_add ($data)
{
	return "message from add";
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
			header('Content-Type: application/xml; charset=ISO-8859-1');
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


//$_GET = array('action' => 'delete', 'data' => '42-45');
echo route_main();

