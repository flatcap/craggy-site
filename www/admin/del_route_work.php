<?php

set_include_path ('../../libs');

include 'db.php';
include 'db_names.php';
include 'utils.php';

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

function route_list($data)
{
	global $DB_V_ROUTE;

	$output = "";
	$list = array();

	$range = parse_range ($data);
	//var_dump ($range);

	foreach ($range as $set) {
		$start = intval ($set['start']);		// intval = assumption about craggy panel names
		$end   = intval ($set['end']);
		for ($i = $start; $i <= $end; $i++) {
			$list[] = $i;
		}
	}
	//var_dump ($list);

	$table = $DB_V_ROUTE;
	$columns = array('id', 'panel', 'colour', 'grade');
	$where = 'panel in (' . implode (',', $list) . ')';
	$order = 'panel_seq, grade_seq, colour';

	$routes = db_select ($table, $columns, $where, $order);
	//var_dump ($routes);

	$output .= '<?xml-stylesheet type="text/xsl" href="route.xsl"?'.'>';
	$output .= list_render_xml ('route', $routes, $columns);

	return $output;
}

function route_delete ($data, $date)
{
	$ids = array();
	$range = parse_range ($data);
	//var_dump ($range);

	foreach ($range as $set) {
		$start = intval ($set['start']);		// intval = assumption about craggy panel names
		$end   = intval ($set['end']);
		for ($i = $start; $i <= $end; $i++) {
			$ids[] = $i;
		}
	}

	$results = db_route_delete ($ids, $date);

	return "deleted {$results['routes']} routes.";
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

	if (array_key_exists ('date', $_GET)) {
		$date = $_GET['date'];
	} else {
		$date = '';
	}

	// action: delete, list, update
	switch ($action) {
		case 'list':
			header('Content-Type: application/xml; charset=ISO-8859-1');
			$response = route_list($data);
			break;
		case 'delete':
			$response = route_delete($data, $date);
			break;
		default:
			$response = "unknown action";
			break;
	}

	return $response;
}


//$_GET = array('action' => 'delete', 'data' => '42-45');
echo route_main();

