<?php

set_include_path ('../libs');

include_once 'db.php';
include_once 'utils.php';

function process_best ($list)
{
	$result = array();

	$id = 0;
	foreach ($list as $row) {
		if ($row['route_id'] == $id) {
			// duplicate
			$s1 = intval ($result[$id]['success_id']);
			$s2 = intval ($row['success_id']);
			if ($s2 > $s1) {
				$result[$id]['success_id'] = $s2;
			}
		} else {
			// copy the whole row
			$id = $row['route_id'];
			$result[$id] = $row;
			$result[$id]['onsight'] = ($row['success_id'] > 2);	// Generate the onsight column
		}
	}

	return $result;
}

function coverage_get_data ($climber_id)
{
	include 'db_names.php';

	$table   = $DB_ROUTE .
			" left join $DB_CLIMB      on (($DB_CLIMB.route_id      = $DB_ROUTE.id) and (climber_id = {$climber_id}))" .
			" left join $DB_GRADE      on ($DB_ROUTE.grade_id       = $DB_GRADE.id)";

	$columns = array ("$DB_ROUTE.id               as route_id",
			  "panel_id",
			  "$DB_GRADE.sequence         as grade_seq",
			  "success_id");

	$where   = array ('date_end is null');
	$order   = "panel_id, $DB_GRADE.sequence, colour_id, date_climbed";

	$climbs = db_select2($table, $columns, $where, $order);

	$climbs = process_best ($climbs);

	$num_routes  = count ($climbs);
	//printf ("count = %d\n", $num_routes);
	$num_tried   = 0;
	$num_success = 0;
	$num_clean   = 0;
	$num_onsight = 0;
	$num_down    = 0;

	$t = 0;			// Tried
	$s = 0;			// Success
	$c = 0;			// Clean
	$o = 0;			// Onsight
	$d = 0;			// Downclimbed

	$route = null;

	foreach ($climbs as $id => $climb) {
		$r = $climb['route_id'];
		if ($r != $route) {
			if ($t > 0) { $num_tried++;   $t = 0; }
			if ($s > 0) { $num_success++; $s = 0; }
			if ($c > 0) { $num_clean++;   $c = 0; }
			if ($o > 0) { $num_onsight++; $o = 0; }
			if ($d > 0) { $num_down++;    $d = 0; }
			$route = $r;
		}

		if (!empty ($climb['success_id'])) $t++;
		if ($climb['success_id'] >= 2)     $s++;
		if ($climb['success_id'] >= 3)     $c++;
		if ($climb['onsight']    == 1)     $o++;
		if ($climb['success_id'] == 4)     $d++;
	}

	if ($t > 0) $num_tried++;
	if ($s > 0) $num_success++;
	if ($c > 0) $num_clean++;
	if ($o > 0) $num_onsight++;
	if ($d > 0) $num_down++;

	$f_tried   = sprintf ('%1.1F%%', $num_tried   / $num_routes * 100);
	$f_success = sprintf ('%1.1F%%', $num_success / $num_routes * 100);
	$f_clean   = sprintf ('%1.1F%%', $num_clean   / $num_routes * 100);
	$f_onsight = sprintf ('%1.1F%%', $num_onsight / $num_routes * 100);
	$f_down    = sprintf ('%1.1F%%', $num_down    / $num_routes * 100);

	$p_tried   = 95;
	$p_success = 90;
	$p_clean   = 85;
	$p_onsight = 70;
	$p_down    = 65;

	$t_tried   = ceil ($num_routes * $p_tried   / 100) - $num_tried;
	$t_success = ceil ($num_routes * $p_success / 100) - $num_success;
	$t_clean   = ceil ($num_routes * $p_clean   / 100) - $num_clean;
	$t_onsight = ceil ($num_routes * $p_onsight / 100) - $num_onsight;
	$t_down    = ceil ($num_routes * $p_down    / 100) - $num_down;

	$output = array();

	$c = array ('Routes', $num_routes, 'Done', 'Target', 'To Do');

	array_push ($output, array ($c[0] => 'Tried',   $c[1] => $num_tried,   $c[2] => $f_tried,   $c[3] => "$p_tried%",   $c[4] => $t_tried));
	array_push ($output, array ($c[0] => 'Success', $c[1] => $num_success, $c[2] => $f_success, $c[3] => "$p_success%", $c[4] => $t_success));
	array_push ($output, array ($c[0] => 'Clean',   $c[1] => $num_clean,   $c[2] => $f_clean,   $c[3] => "$p_clean%",   $c[4] => $t_clean));
	array_push ($output, array ($c[0] => 'Onsight', $c[1] => $num_onsight, $c[2] => $f_onsight, $c[3] => "$p_onsight%", $c[4] => $t_onsight));
	array_push ($output, array ($c[0] => 'Down',    $c[1] => $num_down,    $c[2] => $f_down,    $c[3] => "$p_down%",    $c[4] => $t_down));

	return $output;
}

function coverage_main ($options, $climber_id)
{
	$list = coverage_get_data ($climber_id);

	$columns = get_columns ($list[0]);

	// calculate widths (include headers?)
	$widths = column_widths ($list, $columns, true);
	$widths['Routes'] *= -1;

	$count = $list[0]['To Do'] + $list[1]['To Do'] + $list[2]['To Do'];
	$output = '';
	switch ($options['format']) {
		case 'html':
			$last_update = date ('j M Y', strtotime (db_get_last_update()));

			$output .= html_header ('Coverage', '../');
			$output .= '<body>';
			$output .= html_menu('../');

			$output .= "<div class='content'>\n";

			$output .= "<div class='title'>";
			$output .= "<h1>Coverage</h1>\n";
			$output .= "<span class='download'>";
			$output .= '<h3>Route Data</h3>';
			$output .= "<a href='?format=text'><img alt='coverage as a formatted text document' width='32' height='32' src='../style/txt.png'></a>";
			$output .= "<a href='?format=csv'><img alt='coverage as a csv document' width='32' height='32' src='../style/ss.png'></a>";
			$output .= '</span>';
			$output .= '</div>';

			$output .= "<h2>Coverage <span>($count to go)</span></h2>\n";
			$output .= list_render_html ($list, $columns, $widths, '{sortlist: [[1,1]]}');
			$output .= '</div>';
			$output .= get_errors();
			$output .= '</body>';
			$output .= '</html>';
			break;

		case 'csv':
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="coverage.csv"');
			$output .= list_render_csv ($list, $columns);
			break;

		case 'text':
		default:
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="coverage.txt"');
			$output .= "Coverage ($count to go)\n";
			$output .= list_render_text ($list, $columns, $widths);
			break;
	}

	return $output;
}


date_default_timezone_set('Europe/London');

$format = array ('csv', 'html', 'text');

if (isset ($argc)) {
	$longopts  = array('format:');

	$options = getopt(null, $longopts);

	if (!array_key_exists ('format', $options) || !in_array ($options['format'], $format)) {
		$options['format'] = $format[1];
	}
} else {
	$options = array();

	$f = get_url_variable ('format');
	if (!in_array ($f, $format))
		$f = $format[1];

	$options['format'] = $f;
}

$climber_id = 1;
echo coverage_main ($options, $climber_id);

