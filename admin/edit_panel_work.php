<?php

date_default_timezone_set('Europe/London');

set_include_path ('../libs');

include_once 'utils.php';
include_once 'db.php';
include_once 'xml.php';
include_once 'number.php';
include_once 'height.php';
include_once 'taglist.php';
include_once 'climb_type.php';

include 'db_names.php';

function panel_commit ($db, $xml, $id, $name, $sequence, $climb_type_id, $height, $tags)
{
	global $DB_PANEL;
	$query  = "update $DB_PANEL set " .
		  "name          = '$name', " .
		  "sequence      = $sequence, " .
		  "climb_type_id = $climb_type_id, " .
		  "height        = $height, " .
		  "tags          = '$tags' " .
		  "where id      = $id";

	$result = $db->query($query);
	if ($result !== true) {
		xml_add_error ($xml, sprintf ("Failed to update panel_id %d", $id));
	}

	return $result;
}

function panel_do_list ($db, &$xml)
{
	global $_GET;
	global $DB_PANEL;
	global $DB_CLIMB_TYPE;

	if (!isset ($_GET)) {
		echo 'NO GET';
		return;
	}

	if (array_key_exists ('data', $_GET)) {
		$data = $_GET['data'];
	} else {
		$data = '';
	}

	if (array_key_exists ('seq', $_GET)) {
		$seq = $_GET['seq'];
	} else {
		$seq = '';
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

	$table = "$DB_PANEL left join $DB_CLIMB_TYPE on (climb_type_id = climb_type.id)";
	$columns = array("$DB_PANEL.id as id", 'sequence', 'name', 'climb_type', 'height', 'tags');
	if (empty ($seq) || ($seq == 'false')) {
		$where = 'name in (' . implode (',', $list) . ')';
	} else {
		$where = 'sequence in (' . implode (',', $list) . ')';
	}
	$order = 'sequence';

	$routes = db_select ($db, $table, $columns, $where, $order);
	process_height_abbreviate ($routes);
	//print_r ($routes);

	$columns[0] = 'id';
	list_render_xml3 ($xml, 'panel', $routes, $columns);
}

function panel_do_save($db)
{
	global $_GET;

	if (!array_key_exists ('panel_xml', $_GET)) {
		$xml = xml_new_string ("list");
		$xml->addAttribute ('type', 'panel');
		xml_add_error ($xml, "No panel xml");
		return $xml;
	} else {
		$panels = trim ($_GET['panel_xml']);
		if (empty ($panels)) {
			$xml = xml_new_string ("list");
			$xml->addAttribute ('type', 'panel');
			xml_add_error ($xml, "Empty panels");
			return;
		}
	}

	$xml = simplexml_load_string ($panels);

	for ($i = 0; $i < $xml->count(); $i++) {
		$a = $xml->panel[$i];

		$id       = urldecode ($a->id);
		$name     = urldecode ($a->name);
		$sequence = urldecode ($a->sequence);
		$height   = urldecode ($a->height);
		$height = substr ($height, 0, -1);	//XXX lose the 'm'
		$tags     = urldecode ($a->tags);

		$climb_type = climb_type_match (urldecode ($a->climb_type));
		$climb_type_id = $climb_type['id'];

		panel_commit ($db, $a, $id, $name, $sequence, $climb_type_id, $height, $tags);
	}

	return $xml;
}


function panel_main()
{
	global $_GET;

	if (!array_key_exists ('action', $_GET)) {
		$xml = xml_new_string ("list");
		$xml->addAttribute ('type', 'panel');
		xml_add_error ($xml, "No action");
		return $xml;
	}

	$action  = $_GET['action'];

	$db = db_get_database();

	switch ($action) {
	case 'list':
		$xml = xml_new_string ("list");
		$xml->addAttribute ('type', 'panel');
		panel_do_list ($db, $xml);
		break;
	case 'save':
		$xml = panel_do_save($db);
		break;
	default:
		$xml = xml_new_string ("list");
		$xml->addAttribute ('type', 'panel');
		xml_add_error ($xml, sprintf ("'%s' is not a valid action", $action));
		break;
	}

	return $xml;
}


header('Pragma: no-cache');
header('Content-Type: application/xml; charset=ISO-8859-1');

$xml = panel_main();

echo $xml->asXML();

