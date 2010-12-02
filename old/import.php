<?php

set_include_path ("/home/craggy/www");

include "db.php";
include "utils.php";

function import_open()
{
    global $argc;
    global $argv;

    if ($argc != 2) {
        echo "params\n";
        return FALSE;
    }

    $handle = fopen ($argv[1], "r");
    if ($handle === FALSE) {
        echo "fopen\n";
    }

    return $handle;
}

function import_main($handle)
{
    $headings = array();
    $routes   = array();

    $line = fgetcsv($handle);
    foreach ($line as $id => $col) {
        $headings[$id] = $col;
    }

    do {
        $line = fgetcsv($handle);
        if (empty($line[0]))
            break;

        $r = array();
        foreach ($line as $id => $col) {
            $h = $headings[$id];
            $r[$h] = $col;
        }

        array_push ($routes, $r);
    } while ($line !== FALSE);

    /*
    foreach ($headings as $id => $h) {
        echo "$h,";
    }
    echo "\n";

    foreach ($routes as $id => $r) {
        foreach ($r as $heading => $value) {
            echo "\"$value\",";
        }
        echo "\n";
    }
    */

    $db_routes = array();
    foreach ($routes as $id => $r) {

        $row = array();

        $panel = "";
        if (array_key_exists('Panel', $r))
            $panel = parse_panel($r['Panel'], "id");
        $row['panel'] = $panel;

        $colour = "";
        if (array_key_exists('Colour', $r))
            $colour = parse_colour2($r['Colour'], "id");
        $row['colour'] = $colour;

        $grade = "";
        if (array_key_exists('Grade', $r))
            $grade = parse_grade($r['Grade'], "id");
        $row['grade'] = $grade;

        $notes = "";
        if (array_key_exists('Notes', $r))
            $notes = $r['Notes'];
        $row['notes'] = $notes;

        $setter = "";
        if (array_key_exists('Setter', $r))
            $setter = parse_setter ($r['Setter'], "id");
        $row['setter'] = $setter;

        $date_set = "";
        if (array_key_exists("Date Set", $r))
            $date_set = db_date($r['Date Set']);
        $row['date_set'] = $date_set;

        $date_climbed = "";
        if (array_key_exists("Climbed", $r))
            $date_climbed = db_date($r['Climbed']);
        $row['date_climbed'] = $date_climbed;

        $success = "";
        if (array_key_exists('Success', $r))
            $success = $r['Success'];
        $row['success'] = $success;

        $downclimb = "0";
        if (array_key_exists('D', $r))
            $downclimb = ($r['D'] == "D" ? "1" : "0");
        $row['downclimb'] = $downclimb;

        $nice = "0";
        if (array_key_exists('N', $r))
            $nice = ($r['N'] == "N" ? "1" : "0");
        $row['nice'] = $nice;

        $onsight = "0";
        if (array_key_exists('O', $r))
            $onsight = ($r['O'] == "O" ? "1" : "0");
        $row['onsight'] = $onsight;

        $difficulty = "";
        if (array_key_exists('Diff', $r))
            $difficulty = $r['Diff'];
        $row['difficulty'] = $difficulty;

        $climb_notes = "";
        if (array_key_exists('Climb Notes', $r))
            $climb_notes = $r['Climb Notes'];
        $row['climb_notes'] = $climb_notes;

        array_push ($db_routes, $row);
    }

    db_truncate_route();
    db_route_add2($db_routes);
    db_set_last_update();
}


db_get_database();

$handle = import_open();

$g_climbtypes = db_select ("climb_type");
$g_colours    = db_select ("colour");
$g_grades     = db_select ("grade");
$g_panels     = db_select ("panel");
$g_setters    = db_select ("setter");

import_main($handle);

fclose($handle);

