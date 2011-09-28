<?php

function html_header ($title, $reldir = '')
{
	$output = '';

	$output .= "<!DOCTYPE html PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>";
	$output .= '<html>';
	$output .= '<head>';
	$output .= "<link rel='stylesheet' title='green' href='{$reldir}style/style.css' type='text/css'>";
	$output .= "<link rel='alternate' title='Craggy RSS' href='http://russon.org/craggy/rss.xml' type='application/rss+xml'>";
	$output .= "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
	$output .= "<title>$title - Craggy Island</title>";

	// Google Analytics - not for home server
	if (isset ($_SERVER) && array_key_exists ('SERVER_ADDR', $_SERVER) && ($_SERVER['SERVER_ADDR'] != '192.168.2.2')) {
		$output .= "<script type='text/javascript' src='{$reldir}style/analytics.js'></script>";
	}

	// Table Sorter
	$output .= "<link rel='stylesheet' href='{$reldir}style/tablesorter.css' type='text/css'>";
	$output .= "<script type='text/javascript' src='{$reldir}style/jquery.js'></script>";
	$output .= "<script type='text/javascript' src='{$reldir}style/jquery.metadata.js'></script>";
	$output .= "<script type='text/javascript' src='${reldir}style/jquery.tablesorter.js'></script>";
	$output .= "<script type='text/javascript' src='{$reldir}style/initialise.js'></script>";

	$output .= '</head>';

	return $output;
}

function html_menu($reldir = '')
{
	$rich = false;

	if (isset ($_SERVER)) {
		if (array_key_exists ('PHP_SELF', $_SERVER))
			$rich = strstr ($_SERVER['PHP_SELF'], '/rich/') ||
				strstr ($_SERVER['PHP_SELF'], '/admin/');
		if (!$rich && array_key_exists ('REMOTE_ADDR', $_SERVER))
			$rich = (($_SERVER['REMOTE_ADDR'] == '127.0.0.1') ||
				 ($_SERVER['REMOTE_ADDR'] == '192.168.2.2') ||
				 ($_SERVER['REMOTE_ADDR'] == '192.168.2.3') ||
				 ($_SERVER['REMOTE_ADDR'] == '213.105.7.241'));
	}

	$output  = "<div class='menu'>";

	$output .= "  <img alt='craggy logo' width='135' height='66' src='{$reldir}img/craggy.png'>";
	$output .= '  <h1>Home</h1>';
	$output .= '  <ul>';
	$output .= "    <li><a href='{$reldir}index.php'>Craggy</a></li>";
	$output .= '  </ul>';

	$output .= '  <h1>Routes</h1>';
	$output .= '  <ul>';
	$output .= "    <li><a href='{$reldir}routes.php'>Routes</a></li>";
	$output .= "    <li><a href='{$reldir}6a.php'>6a</a></li>";
	$output .= "    <li><a href='{$reldir}checklist.php'>Checklist</a></li>";
	$output .= "    <li><a href='{$reldir}boards.php'>Boards</a></li>";
	$output .= '  </ul>';

	$output .= '  <h1>Stats</h1>';
	$output .= '  <ul>';
	$output .= "    <li><a href='{$reldir}grades.php'>Grades</a></li>";
	$output .= "    <li><a href='{$reldir}age.php'>Age</a></li>";
	$output .= "    <li><a href='{$reldir}style.php'>Style</a></li>";
	$output .= "    <li><a href='{$reldir}setters.php'>Setters</a></li>";
	$output .= "    <li><a href='{$reldir}colour.php'>Colour</a></li>";
	$output .= '  </ul>';

	if ($rich) {
		$output .= '  <h1>Rich</h1>';
		$output .= '  <ul>';
		$output .= "    <li><a href='{$reldir}rich/climbs.php'>Climbs</a></li>";
		$output .= "    <li><a href='{$reldir}rich/coverage.php'>Coverage</a></li>";
		$output .= "    <li><a href='{$reldir}rich/downclimb.php'>Downclimb</a></li>";
		$output .= "    <li><a href='{$reldir}rich/seldom.php'>Seldom</a></li>";
		$output .= "    <li><a href='{$reldir}rich/todo.php'>To Do</a></li>";
		$output .= "    <li><a href='{$reldir}rich/work.php'>Work</a></li>";
		$output .= '  </ul>';
	}

	if ($rich) {
		$output .= '  <h1>Admin</h1>';
		$output .= '  <ul>';
		$output .= "    <li><a href='{$reldir}admin/setter.php'>Setter</a></li>";
		$output .= "    <li><a href='{$reldir}admin/del_route.php'>Del Route</a></li>";
		$output .= "    <li><a href='{$reldir}admin/add_route.php'>Add Route</a></li>";
		$output .= "    <li><a href='{$reldir}admin/add_climb.php'>Add Climb</a></li>";
		$output .= "    <li><a href='{$reldir}admin/edit_route.php'>Edit Route</a></li>";
		$output .= "    <li><a href='{$reldir}admin/edit_panel.php'>Edit Panel</a></li>";
		$output .= '  </ul>';
	}

	$output .= '</div>';

	return $output;
}

function get_url_variable($name)
{
	$result = false;

	if (isset($_GET)) {
		if (array_key_exists($name, $_GET)) {
			$result = $_GET[$name];
		}
	}

	return $result;
}


function cmp_age($a, $b)
{
	$a1 = strtotime ($a['date_set']);
	$p1 = $a['panel']['number'];
	$g1 = $a['grade']['order'];
	$c1 = $a['colour']['colour'];

	$a2 = strtotime ($b['date_set']);
	$p2 = $b['panel']['number'];
	$g2 = $b['grade']['order'];
	$c2 = $b['colour']['colour'];

	if ($a1 != $a2)
		return ($a1 < $a2) ? -1 : 1;

	if ($p1 != $p2)
		return ($p1 < $p2) ? -1 : 1;

	if ($g1 != $g2)
		return ($g1 < $g2) ? -1 : 1;

	return ($c1 < $c2) ? -1 : 1;
}

function cmp_grade($a, $b)
{
	$g1 = $a['grade_seq'];
	$p1 = $a['panel'];
	$c1 = $a['colour'];

	$g2 = $b['grade_seq'];
	$p2 = $b['panel'];
	$c2 = $b['colour'];

	if ($g1 != $g2)
		return ($g1 < $g2) ? -1 : 1;

	if ($p1 != $p2)
		return ($p1 < $p2) ? -1 : 1;

	return ($c1 < $c2) ? -1 : 1;
}

function cmp_panel($a, $b)
{
	$p1 = $a['panel'];
	$g1 = $a['grade_seq'];
	$c1 = $a['colour'];

	$p2 = $b['panel'];
	$g2 = $b['grade_seq'];
	$c2 = $b['colour'];

	if ($p1 != $p2)
		return ($p1 < $p2) ? -1 : 1;

	if ($g1 != $g2)
		return ($g1 < $g2) ? -1 : 1;

	return ($c1 < $c2) ? -1 : 1;
}

function grade_base ($grade)
{
	$base_len = strspn ($grade, '345678abc');
	return substr ($grade, 0, $base_len);
}


function column_widths ($data, $columns, $header = false, $widths = null)
{
	if (!isset ($widths))
		$widths = array();

	foreach ($columns as $name) {
		$widths[$name] = ($header ? strlen ($name) : 0);
	}

	foreach ($data as $row) {
		foreach ($columns as $name) {
			$widths[$name] = max ($widths[$name], strlen ($row[$name]));
		}
	}

	return $widths;
}

function fix_justification (&$widths)
{
	$columns = array ('climb_notes', 'climb_type', 'colour', 'diff', 'grade', 'notes', 'priority', 'setter', 'success', 'key');

	foreach ($columns as $key) {
		if (array_key_exists ($key, $widths))
			$widths[$key] *= -1;

	}
}

function text_table_header (&$columns, &$widths)
{
	$line   = '------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------';

	$row = array();
	foreach ($columns as $name) {
		$split = explode('_', $name);
		$n = array_pop($split);
		$n = ucfirst ($n);
		$format = sprintf ('%%%ds', $widths[$name]);
		array_push ($row, sprintf ($format, $n));
	}

	$header = implode ($row, ' | ');
	$len = strlen ($header);

	$output = '';

	$output .= $header . "\r\n";
	$output .= sprintf ("%.{$len}s\r\n", $line);

	return $output;
}

function html_table_header ($columns)
{
	$output = '<thead><tr>';

	foreach ($columns as $name) {
		$split = explode('_', $name);
		$n = array_pop($split);
		$n = ucfirst ($n);
		$output .= sprintf ('<th>%s&nbsp;&nbsp;&nbsp;&nbsp;</th>', $n);
	}

	$output .= '</tr></thead>';

	return $output;
}

function csv_table_header (&$columns)
{
	$output = '';

	// XXX maybe leave original db headers?
	$row = array();
	foreach ($columns as $name) {
		$split = explode('_', $name);
		$n = array_pop($split);
		$n = ucfirst ($n);
		array_push ($row, sprintf ('"%s"', $n));
	}

	$output .= implode ($row, ',') . "\r\n";

	return $output;
}

function process_date(&$list, $field, $add_age = false)
{
	// Manipulate dates ('0000-00-00' -> '')
	$today = strtotime('today');
	foreach ($list as $index => $row) {
		$d = $row[$field];
		if (empty ($d) || ($d == '0000-00-00'))
			$d = '';
		else
			$d = date ('j M y', strtotime ($d));
		$list[$index][$field] = $d;

		if ($add_age) {
			if (empty($d)) {
				$a = '';
				$m = '';
			} else {
				$a = floor (($today - strtotime($d)) / 86400);
				$m = sprintf ('%.1f', $a / 30.44);
			}

			$list[$index]['age'] = $a;
			$list[$index]['months'] = $m;
		}
	}

}

function process_type(&$list)
{
	// manipulate data (Lead -> L)
	foreach ($list as $index => $row) {
		if ($row['climb_type'] == 'Lead')
			$list[$index]['climb_type'] = 'L';
		else
			$list[$index]['climb_type'] = '';
	}
}

function process_key (&$list)
{
	$today = strtotime('today');
	foreach ($list as $index => $row) {
		$d = $row['date_set'];
		if ($d == '0000-00-00')
			$d = '';
		$n = $row['notes'];

		if (empty($d)) {
			$a = '';
		} else {
			$a = floor (($today - strtotime($d)) / 86400);
		}

		$k = '';
		if ($row['climb_type'] == 'Lead')
			$k .= 'L';
		if ((!empty($a) && ($a < 32)) || ($a === (float) 0.0))
			$k .= 'N';
		if (!empty($n) && (stristr($n, 'competition') === false))
			$k .= '!';

		$list[$index]['key'] = $k;
	}

}

function get_columns ($row)
{
	$cols = array_keys ($row);

	return $cols;
}

function process_height_abbreviate (&$list)
{
	// manipulate data (8 -> 8m)
	foreach ($list as $key => $row) {
		$list[$key]['height'] = sprintf ('%1.1Fm', $row['height']);
	}
}

function process_height_total (&$list)
{
	$total  = 0.0;

	foreach ($list as $row) {
		$total += $row['height'];
	}

	return $total;
}

function get_stats()
{
	include 'db_names.php';

	$db = db_get_database();
	$output = '';

	$all_routes = db_select($DB_V_ROUTE);

	// Panels stats ----------------------------------------

	usort($all_routes, 'cmp_panel');

	$types = array();
	$count = 0;
	$panel = '';
	foreach ($all_routes as $route) {
		$p = $route['panel'];
		if ($panel == $p)
			continue;
		$panel = $p;
		$t = $route['climb_type'];
		if (array_key_exists ($t, $types)) {
			$types[$t]++;
		} else {
			$types[$t] = 1;
		}
		$count++;
	}

	ksort ($types);

	$output .= "<dl><dt>{$count} Panels</dt>";
	foreach ($types as $name => $count) {
		$output .= "<dd>{$name}: {$count}</dd>";
	}
	$output .= '</dl>';

	// Grades stats ----------------------------------------

	usort($all_routes, 'cmp_grade');

	$grades = array();
	foreach ($all_routes as $g) {
		$base = grade_base($g['grade']);
		if (array_key_exists ($base, $grades))
			$grades[$base]++;
		else
			$grades[$base] = 1;
	}

	$routes = count($all_routes);
	$output .= "<dl><dt>$routes Graded Routes</dt>";
	foreach ($grades as $grade => $count) {
		$output .= "<dd>{$grade}: {$count}</dd>";
	}
	$output .= '</dl>';

	// Other stats ----------------------------------------

	$age_count    = 0;
	$age_total    = 0;
	$route_height = 0;
	$today        = strtotime ('today');
	foreach ($all_routes as $r) {
		$route_height += $r['height'];
		$date = $r['date_set'];
		if (empty ($date) || ($date == '0000-00-00'))
			continue;
		$age_count++;
		$age_total += ($today - strtotime ($date));
	}
	$age_total /= $age_count;
	$age_total /= 86400;
	$age_days   = sprintf ('%d',   $age_total + 0.5);
	$age_months = sprintf ('%.1f', $age_total / 30.44);
	$output .= "Average route age: {$age_days} days ({$age_months} months)<br>";

	$height = sprintf ('%d', $route_height);
	$output.= "Total Route Height: {$height}m<br>";

	// Last Route Set -------------------------------------

	usort($all_routes, 'cmp_age');

	$last = array_pop ($all_routes);
	$date = date ('d M Y', strtotime ($last['date_set']));

	$output .= "Last route setting done on: $date";

	return $output;
}

function partial_match ($partial, $whole)
{
	$lp = strlen ($partial);
	$lw = strlen ($whole);

	if (($lp == 0) || ($lp > $lw))	// partial is empty, or too long
		return false;

	return (strncasecmp ($partial, $whole, $lp) === 0);
}

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


function list_render_html (&$list, &$columns, &$widths, $ts_metadata = null)
{
	$output = '';

	if ($ts_metadata)
		$ts_metadata = " class='tablesorter {$ts_metadata}'";

	$output .= "<table{$ts_metadata}>";
	$output .= html_table_header ($columns);
	$output .= '<tbody>';

	// foreach row of list
	foreach ($list as $row) {

		$output .= '<tr>';

		// foreach col of columns
		foreach ($columns as $col) {

			// consider justification of column
			if ($widths[$col] > 0) {
				$format = '<td class="right">%s</td>';
			} else {
				$format = '<td>%s</td>';
			}
			$output .= sprintf ($format, $row[$col]);
		}

		$output .= '</tr>';
	}

	$output .= '</tbody>';
	$output .= '</table>';

	return $output;
}

function list_render_text (&$list, &$columns, &$widths)
{
	if (count ($list) == 0)
		return '';

	$output = '';

	$output .= text_table_header ($columns, $widths);

	// foreach row of list
	foreach ($list as $row) {

	  $out_row = array();
	  // foreach col of columns
	  foreach ($columns as $col) {

		// consider justification of column
		$format = sprintf ('%%%ds', $widths[$col]);
		array_push ($out_row, sprintf ($format, $row[$col]));
	  }

	  $output .= implode ($out_row, ' | ') . "\r\n";
	}

	return $output;
}

function list_render_csv (&$list, &$columns)
{
	$output = csv_table_header ($columns);

	// foreach row of list
	foreach ($list as $row) {

	  $out_row = array();
	  // foreach col of columns
	  foreach ($columns as $col) {

		// escape quotation marks
		$str = addcslashes ($row[$col], '"');
		array_push ($out_row, sprintf ('"%s"', $str));
	  }

	  $output .= implode ($out_row, ',') . "\r\n";
	}

	return $output;
}

function list_render_xml ($object_name, &$list, &$columns)
{
	$output = "<{$object_name}_list>\n";

	// foreach row of list
	foreach ($list as $row) {
		$output .= "\t<$object_name>\n";

		// foreach col of columns
		foreach ($columns as $col) {
			$output .= sprintf ("\t\t<%s>%s</%s>\n", $col, $row[$col], $col);
		}

		$output .= "\t</$object_name>\n";
	}

	$output .= "</{$object_name}_list>\n";

	return $output;
}

function list_render_xml2 ($object_name, &$list, &$columns)
{
	$output = "<list type='{$object_name}'>\n";

	// foreach row of list
	foreach ($list as $row) {
		$output .= "\t<$object_name>\n";

		// foreach col of columns
		foreach ($columns as $col) {
			$output .= sprintf ("\t\t<%s>%s</%s>\n", $col, $row[$col], $col);
		}

		$output .= "\t</$object_name>\n";
	}

	$output .= "</list>\n";

	return $output;
}

function list_render_xml3 (&$xml, $name, &$list, &$columns)
{
	// foreach row of list
	foreach ($list as $row) {
		$child = $xml->addChild ($name);

		// foreach col of columns
		foreach ($columns as $col) {
			$child->addChild ($col, $row[$col]);
		}
	}
}


function get_errors()
{
	$output = '';
	$errors = error_get_last();
	if (!empty ($errors)) {
		$output .= "<div class='error'>";
		$output .= '<h2>Last Error</h2><pre>';
		$output .= print_r ($errors, true);
		$output .= '</pre></div>';
	}

	return $output;
}

