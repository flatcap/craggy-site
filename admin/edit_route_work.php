<?php

date_default_timezone_set('UTC');

set_include_path ('../libs');

include_once 'utils.php';
include_once 'db.php';
include_once 'xml.php';
include_once 'colour.php';
include_once 'grade.php';
include_once 'panel.php';
include_once 'setter.php';

include 'db_names.php';

function route_commit ($xml, $id, $panel_id, $colour_id, $grade_id, $setter_id, $date, $notes)
{
	$query  = "update route set " .
		  "panel_id  = $panel_id, " .
		  "colour_id = $colour_id, " .
		  "grade_id  = $grade_id, " .
		  "setter_id  = $setter_id, " .
		  "date_set  = '$date', " .
		  "notes  = '$notes' " .
		  "where id = $id";

	$result = mysql_query($query);
	if ($result !== true) {
		xml_add_error ($xml, sprintf ("Failed to update route_id %d", $id));
	}

	return $result;
}

function route_do_list (&$xml)
{
	global $_GET;
	global $DB_V_ROUTE;

	if (!isset ($_GET)) {
		echo 'NO GET';
		return;
	}

	if (array_key_exists ('data', $_GET)) {
		$data = $_GET['data'];
	} else {
		$data = '';
	}

	$range = parse_range ($data);
	//print_r ($range);

	foreach ($range as $set) {
		$start = intval ($set['start']);		// intval = assumption about craggy panel names
		$end   = intval ($set['end']);
		for ($i = $start; $i <= $end; $i++) {
			$list[] = $i;
		}
	}
	//print_r ($list);

	$table = $DB_V_ROUTE;
	$columns = array('id', 'panel', 'colour', 'grade', 'setter', 'date_set', 'notes');
	$where = 'panel in (' . implode (',', $list) . ')';
	$order = 'panel_seq, grade_seq, colour';

	$routes = db_select ($table, $columns, $where, $order);
	//print_r ($routes);

	list_render_xml3 ($xml, 'route', $routes, $columns);
}

function route_do_save()
{
	global $_GET;

	if (!array_key_exists ('route_xml', $_GET)) {
		$xml = xml_new_string ("list");
		$xml->addAttribute ('type', 'route');
		xml_add_error ($xml, "No route xml");
		return $xml;
	} else {
		$climbs = trim ($_GET['route_xml']);
		if (empty ($climbs)) {
			$xml = xml_new_string ("list");
			$xml->addAttribute ('type', 'route');
			xml_add_error ($xml, "Empty climbs");
			return;
		}
	}

	$xml = simplexml_load_string ($climbs);
	//print_r ($xml);

	for ($i = 0; $i < $xml->count(); $i++) {
		$a = $xml->route[$i];

		$id     = urldecode ($a->id);
		$panel  = panel_match  (urldecode ($a->panel));
		$colour = colour_match (urldecode ($a->colour));
		$grade  = grade_match  (urldecode ($a->grade));
		$setter = setter_match (urldecode ($a->setter));
		$date   = urldecode ($a->date_set);
		$notes  = urldecode ($a->notes);

		/*
		print_r ($id);
		print_r ($panel);
		print_r ($colour);
		print_r ($grade);
		*/

		route_commit ($a, $id, $panel['id'], $colour['id'], $grade['id'], $setter['id'], $date, $notes);
	}

	return $xml;
}


function route_main()
{
	global $_GET;

	if (!array_key_exists ('action', $_GET)) {
		$xml = xml_new_string ("list");
		$xml->addAttribute ('type', 'route');
		xml_add_error ($xml, "No action");
		return;
	}

	$action  = $_GET['action'];

	switch ($action) {
	case 'list':
		$xml = xml_new_string ("list");
		$xml->addAttribute ('type', 'route');
		route_do_list ($xml);
		break;
	case 'save':
		$xml = route_do_save();
		break;
	default:
		$xml = xml_new_string ("list");
		$xml->addAttribute ('type', 'route');
		xml_add_error ($xml, sprintf ("'%s' is not a valid action", $action));
		break;
	}

	return $xml;
}


header('Pragma: no-cache');
header('Content-Type: application/xml; charset=ISO-8859-1');

$xml = route_main();

//echo htmlentities ($xml->asXML());
echo $xml->asXML();

