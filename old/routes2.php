<?php

include "db.php";
include "utils.php";

function mark_age ($row, &$old_value)
{
    $v = strtotime ($row['date_set']);

    $result = (abs($v - $old_value) > 7);
    if ($result)
        $old_value = $v;

    return $result;
}

function mark_colour ($row, &$old_value)
{
    $v = $row['colour'];

    $result = ($v != $old_value);
    if ($result)
        $old_value = $v;

    return $result;
}

function mark_grade ($row, &$old_value)
{
    $v = $row['grade'];

    $result = ($v != $old_value);
    if ($result)
        $old_value = $v;

    return $result;
}

function mark_panel ($row, &$old_value)
{
    $v = $row['panel'];

    $result = ($v != $old_value);
    if ($result)
        $old_value = $v;

    return $result;
}

function mark_setter ($row, &$old_value)
{
    $v = $row['setter'];

    $result = ($v != $old_value);
    if ($result)
        $old_value = $v;

    return $result;
}


function rich_6a($html = FALSE)
{
    $table   = "v_route";
    $columns = array ("panel", "colour", "grade", "difficulty", "height");
    $where   = array ("grade_num >= 400", "grade_num < 500", "climb_type <> 'Lead'");

    $list = db_select($table, $columns, $where);

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

function list_main ($html, $type)
{
    $last_update = date ("j M Y", strtotime (db_get_last_update()));

    $table   = "v_route";
    $columns = array ("panel", "colour", "grade", "climb_type", "notes", "setter", "date_set");
    $where   = NULL;

    switch ($type) {
        case "age":    $order = "date_set, panel, grade_num, colour"; $mark = "mark_age";    break;
        case "grade":  $order = "grade_num, panel, colour";           $mark = "mark_grade";  break;
        case "setter": $order = "setter, panel, grade, colour";       $mark = "mark_setter"; break;
        default:       $order = "panel, grade_num, colour";           $mark = "mark_panel";  break;
    }

    $list = db_select($table, $columns, $where, $order);

    array_push ($columns, "age");
    array_push ($columns, "months");
    array_push ($columns, "key");

    // Manipulate dates ("0000-00-00" -> "")
    $today = strtotime("today");
    foreach ($list as $index => $row) {
        $d = $row["date_set"];
        if ($d == "0000-00-00")
            $d = "";
        else
            $d = date ("j M y", strtotime ($d));
        $list[$index]["date_set"] = $d;

        if (empty($d)) {
            $a = "";
            $m = "";
        } else {
            $a = floor (($today - strtotime($d)) / 86400);
            $m = sprintf ("%.1f", $a / 30.44);
        }

        $k = "";
        if ($row["climb_type"] == "Lead")
            $k .= "L";
        if ((!empty($a) && ($a < 32)) || ($a === (float) 0.0))
            $k .= "N";
        if (!empty($n) && (stristr($n, "competition") === FALSE))
            $k .= "!";

        $list[$index]["age"] = $a;
        $list[$index]["months"] = $m;
        $list[$index]["key"] = $k;
    }

    // calculate widths (include headers?)
    $widths = column_widths ($list, $columns, FALSE);

    // alter justification of widths
    fix_justification ($widths);

    $output = "";

    // generate format string (based on column names)
    $f = format_string2 ($widths, $html);
    $format = array();
    $format[0] = "<tr>$f</tr>";
    $format[1] = "<tr class='mark'>$f</tr>";

    if ($html)
        $output .= html_table_header ($columns);

    // print data (based on column names)
    $old_mark = "";
    $toggle = 1;
    foreach ($list as $row) {
        if ($mark ($row, $old_mark))
            $toggle = 1 - $toggle;

        $output .= sprintf ($format[$toggle], $row["panel"], $row["colour"], $row["grade"], $row["climb_type"], $row["notes"], $row["setter"], $row["date_set"], $row["age"], $row["months"], $row["key"]);
    }

    return $output;
}


date_default_timezone_set("UTC");

$output = "";
$html = TRUE;

$type = get_url_variable('type');

$last_update = date ("j M Y", strtotime (db_get_last_update()));

if ($html) {
    $output  = html_header ("Craggy Routes");
    $output .= "<body>";
    $output .= "<div class='header'>Craggy Routes <span>(Last updated: $last_update)</span></div>\n";
    $output .= html_menu();
    $output .= "<div class='content'>\n";
}

$type = get_url_variable('type');
if ($type == "6a") {
    $output .= rich_6a($html);
} else {
    $output .= list_main ($html, $type);
}

if ($html) {
    $output .= "</table>";
    $output .= "</div>";
    $output .= get_errors();
    $output .= "</body>";
    $output .= "</html>";
}

echo $output;

