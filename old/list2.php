function list_6a($html = FALSE)
{
    $table   = "v_route";
    $columns = array ("panel", "colour", "grade", "difficulty", "height");
    $where   = array ("grade_seq >= 400", "grade_seq < 500", "climb_type <> 'Lead'");
    $order   = "panel, grade_seq, colour";

    $list = db_select($table, $columns, $where, $order);

    $output = "";
    $count  = 0;
    $total  = 0.0;

    // manipulate data (800 -> 8m)
    foreach ($list as $key => $row) {
        $height = $row['height'] / 100.0;
        $list[$key]['height'] = sprintf ("%1.1Fm", $height);
        $total += $height;
        $count++;
    }

    // calculate widths (include headers?)
    $widths = column_widths ($list, $columns, FALSE);

    // alter justification of widths
    fix_justification ($widths);

    // generate format string (based on column names)
    $format = format_string ($widths, $html);

    if ($html)
        $output .= html_table_header ($columns);

    // print data (based on column names)
    //print_table ($list);
    foreach ($list as $row) {
        $output .= sprintf ($format, $row["panel"], $row["colour"], $row["grade"], $row["difficulty"], $row["height"]);
    }

    if ($html)
        $output .= "</table>";

    $climbs = "$count climbs (" . sprintf ("%1.1F", $total) . "m)";
    if ($html)
        $climbs = "<p>$climbs</p>";
    else
        $climbs = "$climbs\n";
    $output .= $climbs;

    return $output;
}

function list_age($routes)
{
    usort($routes, "cmp_age");

    $today = strtotime("today");
    $output  = "<table border='1' cellpadding='3' cellspacing='0'>";
    $output .= "<tr>";
    $output .= "<th>Panel</th>";
    $output .= "<th>Colour</th>";
    $output .= "<th>Grade</th>";
    $output .= "<th>Type</th>";
    $output .= "<th>Notes</th>";
    $output .= "<th>Setter</th>";
    $output .= "<th>Date</th>";
    $output .= "<th>Age</th>";
    $output .= "<th>Months</th>";
    $output .= "<th>Key</th>";
    $output .= "</tr>";

    $row_mark = FALSE;
    $old_age = 0;

    foreach ($routes as $id => $route) {

        $p = $route['panel']['number'];
        $c = $route['colour']['colour'];
        $g = $route['grade']['grade'];
        $t = $route['type']['type'];
        $s = $route['setter']['name'];
        $n = $route['notes'];
        $d = $route['date_set'];
        $k = "";

        if ($d == "0000-00-00")
            $d = "";
        else
            $d = date ("j M y", strtotime ($d));

        if (empty($d)) {
            $a = "";
            $m = "";
        } else {
            $a = floor (($today - strtotime($d)) / 86400);
            $m = sprintf ("%.1f", $a / 30.44);
        }

        if ($t == "Lead")
            $k .= "L";
        if ((!empty($a) && ($a < 32)) || ($a === (float) 0.0))
            $k .= "N";
        if (!empty($n) && (stristr($n, "competition") === FALSE))
            $k .= "!";

        if (empty($n)) $n = "&nbsp;";
        if (empty($s)) $s = "&nbsp;";
        if (empty($k)) $k = "&nbsp;";
        if (empty($d)) { $d = "&nbsp;"; $a = "&nbsp;"; }

        if (abs($a - $old_age) > 7) {
            $row_mark = !$row_mark;
            $old_age = $a;
        }

        if ($row_mark)
            $mark = " class='mark'";
        else
            $mark = "";

        $output .= "<tr$mark>";
        $output .= "<td class='right'>$p</td>";
        $output .= "<td>$c</td>";
        $output .= "<td>$g</td>";
        $output .= "<td>$t</td>";
        $output .= "<td>$n</td>";
        $output .= "<td>$s</td>";
        $output .= "<td>$d</td>";
        $output .= "<td class='right'>$a</td>";
        $output .= "<td class='right'>$m</td>";
        $output .= "<td>$k</td>";
        $output .= "</tr>";
    }
    $output .= "</table>";

    return $output;
}

function list_grade($routes)
{
    usort($routes, "cmp_grade");

    $today = strtotime("today");
    $output  = "<table border='1' cellpadding='3' cellspacing='0'>";
    $output .= "<tr>";
    $output .= "<th>Panel</th>";
    $output .= "<th>Colour</th>";
    $output .= "<th>Grade</th>";
    $output .= "<th>Type</th>";
    $output .= "<th>Notes</th>";
    $output .= "<th>Setter</th>";
    $output .= "<th>Date</th>";
    $output .= "<th>Age</th>";
    $output .= "<th>Months</th>";
    $output .= "<th>Key</th>";
    $output .= "</tr>";

    $row_mark = TRUE;
    $old_grade = "";

    foreach ($routes as $id => $route) {

        $p = $route['panel']['number'];
        $c = $route['colour']['colour'];
        $g = $route['grade']['grade'];
        $t = $route['type']['type'];
        $s = $route['setter']['name'];
        $n = $route['notes'];
        $d = $route['date_set'];
        $k = "";

        if ($d == "0000-00-00")
            $d = "";
        else
            $d = date ("j M y", strtotime ($d));

        if (empty($d)) {
            $a = "";
            $m = "";
        } else {
            $a = floor (($today - strtotime($d)) / 86400);
            $m = sprintf ("%.1f", $a / 30.44);
        }

        if ($t == "Lead")
            $k .= "L";
        if ((!empty($a) && ($a < 32)) || ($a === (float) 0.0))
            $k .= "N";
        if (!empty($n) && (stristr($n, "competition") === FALSE))
            $k .= "!";

        if (empty($n)) $n = "&nbsp;";
        if (empty($s)) $s = "&nbsp;";
        if (empty($k)) $k = "&nbsp;";
        if (empty($d)) { $d = "&nbsp;"; $a = "&nbsp;"; }

        if ($old_grade != $g) {
            $row_mark = !$row_mark;
            $old_grade = $g;
        }

        if ($row_mark)
            $mark = " class='mark'";
        else
            $mark = "";

        $output .= "<tr$mark>";
        $output .= "<td class='right'>$p</td>";
        $output .= "<td>$c</td>";
        $output .= "<td>$g</td>";
        $output .= "<td>$t</td>";
        $output .= "<td>$n</td>";
        $output .= "<td>$s</td>";
        $output .= "<td>$d</td>";
        $output .= "<td class='right'>$a</td>";
        $output .= "<td class='right'>$m</td>";
        $output .= "<td>$k</td>";
        $output .= "</tr>";
    }
    $output .= "</table>";

    return $output;
}

function list_panel($routes)
{
    usort($routes, "cmp_panel");

    $today = strtotime("today");
    $output  = "<table border='1' cellpadding='3' cellspacing='0'>";
    $output .= "<tr>";
    $output .= "<th>Panel</th>";
    $output .= "<th>Colour</th>";
    $output .= "<th>Grade</th>";
    $output .= "<th>Type</th>";
    $output .= "<th>Notes</th>";
    $output .= "<th>Setter</th>";
    $output .= "<th>Date</th>";
    $output .= "<th>Age</th>";
    $output .= "<th>Months</th>";
    $output .= "<th>Key</th>";
    $output .= "</tr>";

    $row_mark = TRUE;
    $old_panel = 0;

    foreach ($routes as $id => $route) {

        $p = $route['panel']['number'];
        $c = $route['colour']['colour'];
        $g = $route['grade']['grade'];
        $t = $route['type']['type'];
        $s = $route['setter']['name'];
        $n = $route['notes'];
        $d = $route['date_set'];
        $k = "";

        if ($d == "0000-00-00")
            $d = "";
        else
            $d = date ("j M y", strtotime ($d));

        if (empty($d)) {
            $a = "";
            $m = "";
        } else {
            $a = floor (($today - strtotime($d)) / 86400);
            $m = sprintf ("%.1f", $a / 30.44);
        }

        if ($t == "Lead")
            $k .= "L";
        if ((!empty($a) && ($a < 32)) || ($a === (float) 0.0))
            $k .= "N";
        if (!empty($n) && (stristr($n, "competition") === FALSE))
            $k .= "!";

        if (empty($n)) $n = "&nbsp;";
        if (empty($s)) $s = "&nbsp;";
        if (empty($k)) $k = "&nbsp;";
        if (empty($d)) { $d = "&nbsp;"; $a = "&nbsp;"; }

        if ($old_panel != $p) {
            $row_mark = !$row_mark;
            $old_panel = $p;
        }

        if ($row_mark)
            $mark = " class='mark'";
        else
            $mark = "";

        $output .= "<tr$mark>";
        $output .= "<td class='right'>$p</td>";
        $output .= "<td>$c</td>";
        $output .= "<td>$g</td>";
        $output .= "<td>$t</td>";
        $output .= "<td>$n</td>";
        $output .= "<td>$s</td>";
        $output .= "<td>$d</td>";
        $output .= "<td class='right'>$a</td>";
        $output .= "<td class='right'>$m</td>";
        $output .= "<td>$k</td>";
        $output .= "</tr>";
    }
    $output .= "</table>";

    return $output;
}


