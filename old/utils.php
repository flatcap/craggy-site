function get_post_variable($name)
{
	$result = "";

	if (isset($_POST)) {
		if (array_key_exists($name, $_POST)) {
			$result = $_POST[$name];
		} else {
			if (($name == "button") && array_key_exists('action', $_POST)) {
				$result = $_POST['action'];
			}
		}
	}

	return $result;
}

function html_set_focus($name)
{
    $output  = "<script type='text/javascript'>";
    $output .= "document.getElementById('{$name}').focus();";
    $output .= "</script>";

    return $output;
}

function parse_range ($string)
{
    $delim = ", \n\t";
    $ranges = array();

    $tok = strtok($string, $delim);

    while ($tok !== FALSE) {
        $pos = strpos ($tok, '-');
        if ($pos !== FALSE) {
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

function parse_routes($string, $defaults)
{
    $delim = ", \t";
    $count = 0;
    $routes = array();

    $lines = explode ("\n", $string);

    foreach ($lines as $line) {
        $items = preg_split("/[{$delim}]/", trim($line));

        $panel = 0;
        foreach ($items as $key => $value) {
            if (empty ($value))
                continue;
            if (is_numeric ($value)) {
                $panel = $value;
            } else {
                $colour = parse_colour($value);
                if (empty ($colour))
                    $colour = $value;
                $r = array();
                foreach ($defaults as $key => $value) {
                    $r[$key] = $value;
                }
                $count++;
                $r['id']     = $count;
                $r['panel']  = $panel;
                $r['colour'] = $colour;
                $r['set']    = TRUE;
                array_push ($routes, $r);
            }
        }
    }

    $r = array();
    foreach ($defaults as $key => $value) {
        $r[$key] = $value;
    }

    return $routes;
}

function cmp_checklist($a, $b)
{
	$b1 = grade_block ($a['grade_seq']);
	$p1 = $a['panel'];
	$g1 = $a['grade'];
	$c1 = $a['colour'];

	$b2 = grade_block ($b['grade_seq']);
	$p2 = $b['panel'];
	$g2 = $b['grade'];
	$c2 = $b['colour'];

	if ($b1 != $b2)
		return ($b1 < $b2) ? -1 : 1;

	if ($p1 != $p2)
		return ($p1 < $p2) ? -1 : 1;

	if ($g1 != $g2)
		return ($g1 < $g2) ? -1 : 1;

	return ($c1 < $c2) ? -1 : 1;
}

function format_string ($widths, $html = FALSE)
{
	$format   = "";
	$wstrings = array();

	foreach ($widths as $name => $width) {
		if ($html) {
			if ($width > 0) {
				$format .= '<td class="right">%s</td>';
			} else {
				$format .= '<td>%s</td>';
			}
		} else {
			array_push ($wstrings, "%{$width}s");
		}
	}

	if ($html)
		$format = "<tr>$format</tr>";
	else
		$format = implode ($wstrings, " ") . "\n";

	return $format;
}

function format_string2 ($widths, $html = FALSE)
{
	$format   = "";
	$wstrings = array();

	foreach ($widths as $name => $width) {
		if ($html) {
			if ($width > 0) {
				$format .= '<td class="right">%s</td>';
			} else {
				$format .= '<td>%s</td>';
			}
		} else {
			array_push ($wstrings, "%{$width}s");
		}
	}

	if (!$html)
		$format = implode ($wstrings, " ");

	return $format;
}

function get_url_boolean($name)
{
	$result = FALSE;

	if (isset($_GET)) {
		if (array_key_exists($name, $_GET)) {
			$result = TRUE;
		}
	}

	return $result;
}

