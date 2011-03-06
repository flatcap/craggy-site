<?php

set_include_path ('../../libs');

include 'db.php';
include 'db_names.php';
include 'utils.php';
include 'xml.php';

function parse_range ($string)
{
    $delim = ", \n\t";
    $ranges = array();

    $tok = strtok($string, $delim);

    while ($tok !== false) {
        $pos = strpos ($tok, '-');
        if ($pos !== false) {
            $start = substr ($tok, 0, $pos);
            $end   = substr ($tok, $pos+1);
        } else {
            $start = $tok;
            $end   = $tok;
        }

        if (is_numeric ($start) && is_numeric ($end) && ($end >= $start)) {
            $a = array();
            $a['start'] = $start;
            $a['end']   = $end;
            array_push ($ranges, $a);
        }

        $tok = strtok($delim);
    }

    return $ranges;
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
	$columns = array('id', 'panel', 'colour', 'grade');
	$where = 'panel in (' . implode (',', $list) . ')';

	$routes = db_select ($table, $columns, $where);
	//print_r ($routes);

	list_render_xml3 ($xml, 'route', $routes, $columns);
	//print_r ($xml);
}

function route_do_save (&$xml)
{
}


function route_main (&$xml)
{
	global $_GET;

	if (!array_key_exists ('action', $_GET)) {
		xml_add_error ($xml, "No action");
		return;
	}

	$action  = $_GET['action'];

	switch ($action) {
	case 'list':
		route_do_list ($xml);
		break;
	case 'save':
		route_do_save ($xml);
		break;
	default:
		xml_add_error ($xml, sprintf ("'%s' is not a valid action", $action));
		break;
	}
}


header('Content-Type: application/xml; charset=ISO-8859-1');
$xml = xml_new_string ("list");
$xml->addAttribute ('type', 'route');

route_main ($xml);

echo $xml->asXML();

