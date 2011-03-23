<?php

date_default_timezone_set('UTC');

set_include_path ('../../libs');

include 'utils.php';
include 'db.php';
include 'db_names.php';
include 'cache.php';
include 'colour.php';
include 'date.php';
include 'setter.php';

function parse_grade ($text)
{
	global $DB_GRADE;
	static $grades = null;

	if (!$grades)
		$grades = cache_get_table ($DB_GRADE);

	foreach ($grades as $g) {
		if ($text == $g['grade'])
			return $g;
	}

	return null;
}

function parse_panel ($text)
{
	global $DB_PANEL;
	static $panels = null;

	if (!$panels)
		$panels = cache_get_table ($DB_PANEL);

	foreach ($panels as $p) {
		if ($text == $p['name'])
			return $p;
	}

	return null;
}

function parse_setter ($text)
{
	// Need to match:
	//     initials
	//     unique first_name
	//     unique surname
	//     first_name surname

	global $DB_SETTER;
	static $setters = null;

	if (empty ($text))
		return null;
	if (!$setters)
		$setters = cache_get_table ($DB_SETTER);

	$count = 0;
	$match = null;
	$text = strtolower ($text);
	foreach ($setters as $s) {
		$inits   = strtolower ($s['initials']);
		$first   = strtolower ($s['first_name']);
		$surname = strtolower ($s['surname']);
		$whole   = "$first $surname";
		if ($text == $whole)
			return $s;
		if ($text == $inits)
			return $s;
		if ($text == $first) {
			$count++;
			$match = $s;
		}
		if ($text == $surname) {
			$count++;
			$match = $s;
		}
	}

	// check there's only one match
	if ($count == 1)
		return $match;

	return null;
}


function valid_colour (&$route)
{
	$c = colour_match (urldecode ($route->colour));
	if ($c !== null) {
		$route->colour = $c['colour'];
		$route->colour_id = $c['id'];
		return true;
	} else {
		$route->addChild ('message', sprintf ("'%s' is not a valid colour", $route->colour));
		return false;
	}
}

function valid_date (&$route)
{
	$d = strtotime (urldecode ($route->date));

	if ($d !== false) {
		$now = strtotime ('now');
		if ($d <= $now) {
			$route->date = strftime('%Y-%m-%d', $d);
			return true;
		}
		$route->addChild ('message', 'Date cannot be in the future');
	} else {
		$route->addChild ('message', sprintf ("'%s' is not a valid date", $route->date));
	}

	return false;
}

function valid_grade (&$route)
{
	$g = parse_grade (urldecode ($route->grade));
	if ($g !== null) {
		$route->grade    = $g['grade'];
		$route->grade_id = $g['id'];
		return true;
	} else {
		$route->addChild ('message', sprintf ("'%s' is not a valid grade", $route->grade));
		return false;
	}
}

function valid_notes (&$route)
{
	// check for invalid characters
	$route->notes = urldecode ($route->notes);
	return true;
}

function valid_panel (&$route)
{
	$p = parse_panel (urldecode ($route->panel));
	if ($p !== null) {
		$route->panel    = $p['name'];
		$route->panel_id = $p['id'];
		return true;
	} else {
		$route->addChild ('message', sprintf ("'%s' is not a valid panel", $route->panel));
		return false;
	}
}

function valid_setter (&$route)
{
	$s = parse_setter (urldecode ($route->setter));
	if ($s !== null) {
		$route->setter    = $s['first_name'] . ' ' . $s['surname'];
		$route->setter_id = $s['id'];
		return true;
	} else {
		$route->addChild ('message', sprintf ("'%s' is not a valid setter", $route->setter));
		return false;
	}
}


function validate_route (&$route)
{
	$colour = valid_colour ($route);
	$date   = valid_date   ($route);
	$grade  = valid_grade  ($route);
	$notes  = valid_notes  ($route);
	$panel  = valid_panel  ($route);
	$setter = valid_setter ($route);

	return ($colour && $date && $grade && $notes && $panel && $setter);
}

function db_route_add ($route)
{
	/*
	printf ("panel   = %s (%d)\n", $route->panel,  $route->panel_id);
	printf ("colour  = %s (%d)\n", $route->colour, $route->colour_id);
	printf ("grade   = %s (%d)\n", $route->grade,  $route->grade_id);
	printf ("setter  = %s (%d)\n", $route->setter, $route->setter_id);
	printf ("date    = %s\n",      $route->date);
	printf ("notes   = %s\n",      $route->notes);
	*/

	$query = "insert into route (panel_id,colour_id,grade_id,setter_id,notes,date_set) values ";
	$query .= "($route->panel_id, $route->colour_id, $route->grade_id, $route->setter_id, '$route->notes', '$route->date')";
	//echo $query . "\n";

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


/*
//$_GET = array('action' => 'add', 'data' => '45   red  5+,     gn   6a  ,  bg   6b  ');
if (isset ($argc)) {
} else {
	header('Content-Type: application/xml; charset=ISO-8859-1');
	//echo "<pre>";
}

$xml = file_get_contents ('route.xml');
$_GET = array('action' => 'save', 'data' => $xml);
*/
$result = route_main();
//$result = htmlentities ($result);
echo $result;

