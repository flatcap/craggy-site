<?php

include "../db.php";
include "utils.php";

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
    $g1 = $a['grade']['order'];
    $p1 = $a['panel']['number'];
    $c1 = $a['colour']['colour'];

    $g2 = $b['grade']['order'];
    $p2 = $b['panel']['number'];
    $c2 = $b['colour']['colour'];

    if ($g1 != $g2)
        return ($g1 < $g2) ? -1 : 1;

    if ($p1 != $p2)
        return ($p1 < $p2) ? -1 : 1;

    return ($c1 < $c2) ? -1 : 1;
}

function cmp_panel($a, $b)
{
    $p1 = $a['panel']['number'];
    $g1 = $a['grade']['order'];
    $c1 = $a['colour']['colour'];

    $p2 = $b['panel']['number'];
    $g2 = $b['grade']['order'];
    $c2 = $b['colour']['colour'];

    if ($p1 != $p2)
        return ($p1 < $p2) ? -1 : 1;

    if ($g1 != $g2)
        return ($g1 < $g2) ? -1 : 1;

    return ($c1 < $c2) ? -1 : 1;
}


function user_date($date)
{
    $d = strtotime($date);
    if ($d !== FALSE)
        $result = strftime("%d %b %Y", $d);
    else
        $result = "";

    return $result;
}

function route_array_to_form($routes, $readwrite, $checkbox)
{
    if ($readwrite)
        $ro = "";
    else
        $ro = "readonly ";
    $output  = "<table>\n";
    $output .= "<tr>\n";
    if ($checkbox) {
        $output .= "<th>&#10004;</th>";     // A bold tick
    }
    $output .= "<th>Panel</th>";
    $output .= "<th>Type</th>";
    $output .= "<th>Colour</th>";
    $output .= "<th>Grade</th>";
    $output .= "<th>Notes</th>";
    $output .= "<th>Setter</th>";
    $output .= "<th>Date</th>";
    if (!$readwrite) {
        $output .= "<th>Age</th>";
        $output .= "<th>Months</th>";
        $output .= "<th>Key</th>";
    }
    $output .= "</tr>\n";
    $today = strtotime("today");

    foreach ($routes as $id => $r) {
        /*
        echo "<pre>";
        var_dump($r);
        echo "</pre>";
        break;
        */

        if (array_key_exists ("valid", $r) && ($r['valid'] == FALSE) && ($readwrite == TRUE))
            $output .= "<tr class='mand'>\n";
        else
            $output .= "<tr>\n";

        //$id = $r['id'];
        $date = user_date($r['date_set']);
        $t = $r['type']['type'];
        $d = $date;

        $output .= "<input type='hidden' name='id_{$id}' value='{$id}'>\n";
        if ($checkbox) {
            $output .= "<td><input type='checkbox' class='checkbox' name='set_{$id}'></td>\n";
        }

        $output .= "<td><input {$ro}type='text' size='4' name='panel_{$id}' value='{$r['panel']['number']}'></td>\n";
        $output .= "<td><input {$ro}type='text' size='7' name='type_{$id}' value='{$r['type']['type']}'></td>\n";
        $output .= "<td><input {$ro}type='text' size='10' name='colour_{$id}' value='{$r['colour']['colour']}'></td>\n";
        $output .= "<td><input {$ro}type='text' size='4' name='grade_{$id}' value='{$r['grade']['grade']}'></td>\n";
        $output .= "<td><input {$ro}type='text' size='20' name='notes_{$id}' value='{$r['notes']}'></td>\n";
        $output .= "<td><input {$ro}type='text' size='10' name='setter_{$id}' value='{$r['setter']['name']}'></td>\n";
        $output .= "<td><input {$ro}type='text' size='10' name='date_set_{$id}' value='{$date}'></td>\n";
        if (!$readwrite) {
            if ($d == "0000-00-00")
                $d = "";

            if (empty($d)) {
                $a = "";
                $m = "";
            } else {
                $a = floor (($today - strtotime($d)) / 86400);
                $m = sprintf ("%.1f", $a / 30.44);
            }

            $k = "";
            if ($t == "Lead")
                $k .= "L";
            if (!empty($a) && ($a < 32))
                $k .= "N";
            if (!empty($n) && (stristr($n, "competition") === FALSE))
                $k .= "!";

            if (empty($a)) $a = "&nbsp;";
            if (empty($m)) $m = "&nbsp;";
            if (empty($k)) $k = "&nbsp;";

            $output .= "<td>{$a}</td>";
            $output .= "<td>{$m}</td>";
            $output .= "<td>{$k}</td>";
        }
        $output .= "</tr>\n";
    }

    $output .= "</table>\n";

    return $output;
}

function route_list($routes)
{
    $output = "";
    $sort = get_url_variable('sort');
    $today = strtotime("today");

    switch ($sort) {
        case "age":
        case "date":  usort($routes, "cmp_age");   break;
        case "grade": usort($routes, "cmp_grade"); break;
        case "panel":
        default:      usort($routes, "cmp_panel"); break;
    }

    $output .= "Add Edit Delete Search<br>";

    $output .= route_array_to_form ($routes, FALSE, TRUE);

    return $output;
}

function route_list2($routes)
{
    $output = "";
    $sort = get_url_variable('sort');
    $today = strtotime("today");

    switch ($sort) {
        case "age":
        case "date":  usort($routes, "cmp_age");   break;
        case "grade": usort($routes, "cmp_grade"); break;
        case "panel":
        default:      usort($routes, "cmp_panel"); break;
    }

    $output .= "Add Edit Delete Search<br>";

    $output .= "<table border='1' cellpadding='3' cellspacing='0'>";
    $output .= "<tr>";
    $output .= "<th>&#10004;</th>";     // A bold tick
    $output .= "<th>Panel</th>";
    $output .= "<th>Type</th>";
    $output .= "<th>Colour</th>";
    $output .= "<th>Grade</th>";
    $output .= "<th>Notes</th>";
    $output .= "<th>Setter</th>";
    $output .= "<th>Date</th>";
    $output .= "<th>Age</th>";
    $output .= "<th>Months</th>";
    $output .= "<th>Key</th>";
    $output .= "</tr>";

    foreach ($routes as $id => $route) {
        echo "<pre>";
        var_dump($route);
        echo "</pre>";
        break;


        $p = $route['panel']['number'];
        $t = $route['type']['type'];
        $c = $route['colour']['colour'];
        $g = $route['grade']['grade'];
        $s = $route['setter']['initials'];
        $n = $route['notes'];
        $d = $route['date_set'];
        $k = "";

        if ($d == "0000-00-00")
            $d = "";

        if (empty($d)) {
            $a = "";
            $m = "";
        } else {
            $a = floor (($today - strtotime($d)) / 86400);
            $m = sprintf ("%.1f", $a / 30.44);
        }

        if ($t == "Lead")
            $k .= "L";
        if (!empty($a) && ($a < 32))
            $k .= "N";
        if (!empty($n) && (stristr($n, "competition") === FALSE))
            $k .= "!";

        if (empty($n)) $n = "&nbsp;";
        if (empty($s)) $s = "&nbsp;";
        if (empty($k)) $k = "&nbsp;";
        if (empty($d)) { $d = "&nbsp;"; $a = "&nbsp;"; }

        $output .= "<tr>";
        $output .= "<td><input type='checkbox' class='checkbox' name='set_{$id}'></td>\n";
        $output .= "<td>$p</td>";
        $output .= "<td>$t</td>";
        $output .= "<td>$c</td>";
        $output .= "<td>$g</td>";
        $output .= "<td>$n</td>";
        $output .= "<td>$s</td>";
        $output .= "<td>$d</td>";
        $output .= "<td>$a</td>";
        $output .= "<td>$m</td>";
        $output .= "<td>$k</td>";
        $output .= "</tr>";
    }
    $output .= "</table>";

    return $output;
}

function route_delete($routes)
{
    return "route_delete";
}

function route_edit($routes)
{
    return "route_edit";
}


function route_main()
{
    $output = "";
    $routes = db_get_routes();

    $action = get_url_variable('action');
    switch ($action) {
        case "delete": $output .= route_delete($routes); break;
        case "edit":   $output .= route_edit($routes);   break;
        case "list":
        default:       $output .= route_list($routes);   break;
    }

    return $output;
}


date_default_timezone_set("UTC");
db_get_database();

$output = html_header ("Craggy Routes");
$output .= "<body>\n";
$output .= "<div class='header'>Craggy Routes</div>\n";
$output .= html_menu();
$output .= "<div class='content'>\n";

$output .= route_main();

$output .= "</div>\n";
$output .= get_errors();
$output .= "</body>\n";
$output .= "</html>";

echo $output;

