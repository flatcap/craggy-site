<?php

date_default_timezone_set('UTC');

set_include_path ('../../libs');

include 'utils.php';
include 'db.php';
include 'db_names.php';

include 'colour.php';
include 'date.php';
include 'grade.php';
include 'panel.php';
include 'setter.php';

function validate_route (&$route)
{
	$colour = colour_match_xml ($route, urldecode ($route->colour));
	if ($colour) {
		$route->colour    = $c['colour'];
		$route->colour_id = $c['id'];
	}

	$date = date_match_xml ($route, urldecode ($route->date));

	$grade = grade_match_xml ($route, urldecode ($route->grade));
	if ($grade) {
		$route->grade    = $g['grade'];
		$route->grade_id = $g['id'];
	}

	// XXX check for invalid characters?
	$route->notes = urldecode ($route->notes);

	$panel = panel_match_xml ($route, urldecode ($route->panel));
	if ($panel) {
		$route->panel    = $p['name'];
		$route->panel_id = $p['id'];
	}

	$setter = setter_match_xml ($route, urldecode ($route->setter));
	if ($setter) {
		$route->setter    = $s['name'];
		$route->setter_id = $s['id'];
	}

	return ($colour && $date && $grade && $panel && $setter);
}

function db_route_add ($route)
{
	global $DB_ROUTE
	$query = "insert into $DB_ROUTE (panel_id,colour_id,grade_id,setter_id,notes,date_set) values ";
	$query .= "($route->panel_id, $route->colour_id, $route->grade_id, $route->setter_id, '$route->notes', '$route->date')";

	$db = db_get_database();
	$result = mysql_query($query);
	if ($result === true) {
		$route_id = mysql_insert_id();
	} else {
		$route_id = false;
	}

	return $route_id;
}


function route_add ($date, $setter, $data)
{
	//printf ("data = >>$data<<\n");
	// parse:
	//	45 red 5+, gn 6a, bg 6b into
	// into
	//	45 Red 5+
	//	45 Green 6a
	//	45 Beige 6b

	$message = "";

	$date = date_match ($date, $message);
	if ($date === null) {
		echo $message;
		return null;
	}

	$setter = setter_match ($setter, $message);
	if ($setter === null) {
		echo $message;
		return null;
	}
	$setter = trim ($setter['first_name'] . ' ' . $setter['surname']);

	$routes = array();

	$list = explode (' ', $data, 2);

	$panel = $list[0];
	$data  = $list[1];

	$list = explode (',', $data);
	foreach ($list as $item) {
		$item = trim ($item);
		list ($colour, $grade) = explode (' ', $item, 2);
		$c = colour_match($colour);
		if ($c !== null) {
			$colour = $c['colour'];
		}
		$grade = trim ($grade);
		$routes[] = array ('panel' => $panel, 'colour' => $colour, 'grade' => $grade, 'date' => $date, 'setter' => $setter);
	}

	$columns = array ('panel', 'colour', 'grade', 'date', 'setter');
	$xml = '<?xml version="1.0"?'.">\n";
	$xml .= '<?xml-stylesheet type="text/xsl" href="route.xsl"?'.">\n";
	$xml .= list_render_xml2 ('route', $routes, $columns);
	return $xml;
}

function route_save ($data)
{
	$xml = simplexml_load_string ($data);

	for ($i = 0; $i < $xml->count(); $i++) {
		$message = array();
		$a = $xml->route[$i];
		$route = validate_route ($a);
		if ($route !== false) {
			$route_id = db_route_add ($a);
		} else {
			$route_id = false;
		}

		if ($route_id !== false) {
			$xml->route[$i]->addAttribute ('result', 'valid');
			$xml->route[$i]->addChild ('route_id', $route_id);
		} else {
			$xml->route[$i]->addAttribute ('result', 'invalid');
		}
	}

	return $xml->asXML();
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

	if ($action == 'add') {
		if (!array_key_exists ('date', $_GET)) {
			echo 'NO DATE';
			return;
		}
		$date = $_GET['date'];
		if (!array_key_exists ('setter', $_GET)) {
			echo 'NO SETTER';
			return;
		}
		$setter = $_GET['setter'];
		if (!array_key_exists ('routes', $_GET)) {
			echo 'NO ROUTES';
			return;
		}
		$routes = $_GET['routes'];

	} else if ($action == 'save') {
		if (!array_key_exists ('route_xml', $_GET)) {
			echo 'NO ROUTE_XML';
			return;
		}
		$route_xml = $_GET['route_xml'];
	}

	switch ($action) {
		case 'add':
			header('Content-Type: application/xml; charset=ISO-8859-1');
			$response = route_add($date, $setter, $routes);
			break;
		case 'save':
			header('Content-Type: application/xml; charset=ISO-8859-1');
			$response = route_save($route_xml);
			break;
		default:
			$response = "unknown action";
			break;
	}

	return $response;
}


echo route_main();

