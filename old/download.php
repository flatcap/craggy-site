<?php

include "db.php";
include "utils.php";

function grade_block($grade)
{
    if ($grade[0] < "6")
        return $grade[0];

    $g = substr($grade, 0, 2);
    switch ($g) {
        case "6a": return 6;
        case "6b": return 7;
        default:   return 8;
    }
}

function cmp_checklist($a, $b)
{
    $b1 = grade_block ($a['grade']);
    $p1 = $a['panel'];
    $g1 = $a['grade_num'];
    $c1 = $a['colour'];

    $b2 = grade_block ($b['grade']);
    $p2 = $b['panel'];
    $g2 = $b['grade_num'];
    $c2 = $b['colour'];

    if ($b1 != $b2)
        return ($b1 < $b2) ? -1 : 1;

    if ($p1 != $p2)
        return ($p1 < $p2) ? -1 : 1;

    if ($g1 != $g2)
        return ($g1 < $g2) ? -1 : 1;

    return ($c1 < $c2) ? -1 : 1;
}

function download_checklist()
{
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="checklist.txt"');

    $table   = "v_route";
    $columns = array ("panel", "climb_type", "colour", "grade", "grade_num", "notes", "date_set");
    $where   = NULL;
    $order   = "grade_num,panel,colour";

    $list = db_select($table, $columns, $where, $order);

    usort($list, "cmp_checklist");

    $gb = 0;
    $count = -1;
    $output = "";
    $block = "";

    foreach ($list as $route) {

        $count++;
        $p = $route['panel'];
        $c = $route['colour'];
        $g = $route['grade'];
        $n = $route['notes'];
        $d = $route['date_set'];
        $k = "";

        if ($d == "0000-00-00")
            $d = "";

        if (empty($d)) {
            $a = "";
        } else {
            $a = floor ((strtotime("today") - strtotime($d)) / 86400);
        }

        if ($route['climb_type'] == "Lead")
            $k .= "L";

        if ((!empty($a) && ($a < 32)) || ($a === (float) 0.0))
            $k .= "N";

        if (!empty($n) && (stristr($n, "competition") === FALSE))
            $k .= "!";

        $tmp = grade_block ($g);
        if ($gb != $tmp) {
            switch ($gb) {
                case "0": $block .= "Grade 3"; break;
                case "3": $block .= "\r\nGrade 4"; break;
                case "4": $block .= "\r\nGrade 5"; break;
                case "5": $block .= "\r\nGrade 6a"; break;
                case "6": $block .= "\r\nGrade 6b"; break;
                case "7": $block .= "\r\nGrade 6c..."; break;
            }
            $gb = $tmp;
            if ($count != 0) {
                $output .= " ($count)\r\n";
                $count = 0;
            }
            $output .= $block;
            $block = "";
        }

        $block .= "$p\t$c\t$g\t$k\r\n";
    }
    $count++;
    $output .= " ($count)\r\n";
    $output .= $block;
    return $output;
}

function download_csv()
{
    header('Content-type: text/csv');
    header('Content-Disposition: attachment; filename="routes.csv"');

    $table   = "v_route";
    $columns = array ("panel", "climb_type", "colour", "grade", "notes", "setter", "date_set");
    $where   = NULL;
    $order   = "panel,grade_num,colour";

    $list = db_select($table, $columns, $where, $order);

    // manipulate dates ("0000-00-00" -> "")
    foreach ($list as $key => $row) {
        if ($row["date_set"] == "0000-00-00")
            $list[$key]["date_set"] = "";
    }

    $output .= '"Panel","Type","Colour","Grade","Notes","Setter","Date Set"'."\r\n";
    foreach ($list as $row) {
        $output .= sprintf ('"%s","%s","%s","%s","%s","%s","%s"'."\r\n", $row["panel"], $row["climb_type"], $row["colour"], $row["grade"], $row["notes"], $row["setter"], $row["date_set"]);
    }

    return $output;
}

function download_main()
{
    $type = get_url_variable('type');

    $last_update = date ("j M Y", strtotime (db_get_last_update()));

    if (($type != "checklist") && ($type != "csv")) {
        $output  = html_header ("Craggy Routes");
        $output .= "<body>";
        $output .= "<div class='header'>Craggy Routes <span>(Last updated: $last_update)</span></div>\n";
        $output .= html_menu();
        $output .= "<div class='content'>\n";
    }

    switch ($type) {
        case "checklist": $output .= download_checklist(); break;
        case "csv":       $output .= download_csv();       break;
        default:          $output .= "Unknown URL";        break;
    }

    if (($type != "checklist") && ($type != "csv")) {
        $output .= "</div>";
        $output .= get_errors();
        $output .= "</body>";
        $output .= "</html>";
    }

    return $output;
}


date_default_timezone_set("UTC");

echo download_main();

