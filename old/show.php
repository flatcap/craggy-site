<?php

include "db.php";
include "utils.php";

function show_colour()
{
    global $g_colours;
    $output = "";

    $output  = "<table border='1' cellpadding='3' cellspacing='0'>";
    $output .= "<tr>";
    $output .= "<td>ID</td>";
    $output .= "<td>Colour</td>";
    $output .= "<td>In Use</td>";
    $output .= "<td>Abbreviations</td>";
    $output .= "</tr>";

    foreach ($g_colours as $id => $colour) {
        $c = $colour['colour'];

        $output .= "<tr>";
        $output .= "<td>$id</td>";
        $output .= "<td>$c</td>";
        $output .= "<td>yes</td>";
        $output .= "<td>wibble</td>";
        $output .= "</tr>";

    }

    $output .= "</table>";

    $output .= "</div>";
    $output .= "</html>";

    return $output;
}

function show_climb()
{
    global $g_climbtypes;
    $output = "";

    $output  = "<table border='1' cellpadding='3' cellspacing='0'>";
    $output .= "<tr>";
    $output .= "<td>ID</td>";
    $output .= "<td>Climb Type</td>";
    $output .= "<td>In Use</td>";
    $output .= "</tr>";

    foreach ($g_climbtypes as $id => $climbtype) {
        $t = $climbtype['type'];

        $output .= "<tr>";
        $output .= "<td>$id</td>";
        $output .= "<td>$t</td>";
        $output .= "<td>yes</td>";
        $output .= "</tr>";

    }

    $output .= "</table>";

    $output .= "</div>";
    $output .= "</html>";

    return $output;
}

function grade_row_to_array($row)
{
    $r = array();

    $id = get_post_variable("id_{$row}");
    if (empty($id))
        return FALSE;

    $r['id']    = $id;
    $r['order'] = get_post_variable("order_{$row}");
    $r['grade'] = get_post_variable("grade_{$row}");
    $r['inuse'] = get_post_variable("inuse_{$row}");

    return $r;
}

function grade_form_to_array()
{
    $grades  = array();
    $keys    = array_keys($_POST);
    $matches = array();

    foreach ($keys as $id => $value) {
        preg_match("/(id_)(.*)/", $value, $matches);
        if (empty($matches))
            continue;
        $row = grade_row_to_array($matches[2]);
        if ($row === FALSE)
            break;

        array_push($grades, $row);
    }

    return $grades;
}

function grade_array_to_form($grades, $readwrite, $checkbox)
{
    if ($readwrite)
        $ro = "";
    else
        $ro = "readonly ";
    $output  = "<table>\n";
    $output .= "<tr>\n";
    $output .= "<th>Order</th>";
    $output .= "<th>Grade</th>";
    $output .= "<th>In Use</th>";
    if ($checkbox)
        $output .= "<th>Select</th>\n";
    $output .= "</tr>\n";

    foreach ($grades as $g) {
        if (array_key_exists ("valid", $g) && ($g['valid'] == FALSE) && ($readwrite == TRUE))
            $output .= "<tr class='mand'>\n";
        else
            $output .= "<tr>\n";

        $id = $g['id'];
        $o  = $g['order'];
        $g  = $g['grade'];

        $output .= "<input type='hidden' name='id_{$id}' value='{$g['id']}'>\n";
        $output .= "<td><input {$ro}type='text' size='10' name='order_{$id}' value='{$o}'></td>\n";
        $output .= "<td><input {$ro}type='text' size='4' name='grade_{$id}' value='{$g}'></td>\n";
        $output .= "<td><input {$ro}type='text' size='20' name='inuse_{$id}' value='yes'></td>\n";
        if ($checkbox)
            $output .= "<td><input type='checkbox' class='checkbox' name='set_{$id}'></td>\n";
        $output .= "</tr>\n";
    }

    $output .= "</table>\n";

    return $output;
}

function show_grade()
{
    global $g_grades;
    $output = "";

    $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
    $output .= "<input type='hidden' name='stage' value='grades'>\n";

    $output .= grade_array_to_form ($g_grades, FALSE, FALSE);

    $output .= "<br>\n";
    $output .= "<input name='button' accesskey='e' value='Edit' type='submit'>\n";
    $output .= "<input name='button' accesskey='d' value='Delete' type='submit'>\n";
    $output .= "</form>\n";

    $output .= "</div>";
    $output .= "</html>";

    return $output;
}

function edit_grade()
{
    global $g_grades;
    $output = "";

    $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
    $output .= "<input type='hidden' name='stage' value='grades'>\n";

    $output .= grade_array_to_form ($g_grades, TRUE, FALSE);

    $output .= "<br>\n";
    $output .= "<input name='button' accesskey='v' value='Verify' type='submit'>\n";
    $output .= "<input name='button' accesskey='o' value='OK' type='submit'>\n";
    $output .= "</form>\n";

    $output .= "</div>";
    $output .= "</html>";

    return $output;
}

function verify_grade()
{
    global $g_grades;
    $output = "";

    $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
    $output .= "<input type='hidden' name='stage' value='edit_grades'>\n";

    $output .= grade_array_to_form ($g_grades, TRUE, FALSE);

    $output .= "<br>\n";
    $output .= "<input name='button' accesskey='v' value='Verify' type='submit'>\n";
    $output .= "<input name='button' accesskey='o' value='OK' type='submit'>\n";
    $output .= "</form>\n";

    $output .= "</div>";
    $output .= "</html>";

    return $output;
}

function show_panel()
{
    global $g_panels;
    $output = "";

    $output  = "<table border='1' cellpadding='3' cellspacing='0'>";
    $output .= "<tr>";
    $output .= "<td>ID</td>";
    $output .= "<td>Name</td>";
    $output .= "<td>Type</td>";
    $output .= "<td>Height</td>";
    $output .= "<td>Attributes</td>";
    $output .= "<td>In Use</td>";
    $output .= "</tr>";

    foreach ($g_panels as $id => $panel) {
        $num    = $panel['number'];
        $type   = $panel['type'];
        $height = $panel['height'];

        $output .= "<tr>";
        $output .= "<td>$id</td>";
        $output .= "<td>$num</td>";
        $output .= "<td>$type</td>";
        $output .= "<td>$height</td>";
        $output .= "<td>attrs</td>";
        $output .= "<td>yes</td>";
        $output .= "</tr>";

    }

    $output .= "</table>";

    $output .= "</div>";
    $output .= "</html>";

    return $output;
}

function show_setter()
{
    global $g_setters;
    $output = "";

    $output  = "<table border='1' cellpadding='3' cellspacing='0'>";
    $output .= "<tr>";
    $output .= "<td>ID</td>";
    $output .= "<td>Initials</td>";
    $output .= "<td>Name</td>";
    $output .= "<td>In Use</td>";
    $output .= "</tr>";

    foreach ($g_setters as $id => $setter) {
        $i = $setter['initials'];
        $n = $setter['name'];

        $output .= "<tr>";
        $output .= "<td>$id</td>";
        $output .= "<td>$i</td>";
        $output .= "<td>$n</td>";
        $output .= "<td>yes</td>";
        $output .= "</tr>";

    }

    $output .= "</table>";

    $output .= "</div>";
    $output .= "</html>";

    return $output;
}

function show_main()
{
    $stage  = get_post_variable('stage');
    $button = get_post_variable('button');
    $type = get_url_variable('type');

    if (($stage == "grades") && ($button == "Edit")) {
        return edit_grade();
    }

    if (($stage == "edit_grades") && ($button == "Verify")) {
        return verify_grade();
    }

    switch ($type) {
        case "climb":  return show_climb();
        case "colour": return show_colour();
        case "grade":  return show_grade();
        case "panel":  return show_panel();
        case "setter": return show_setter();
    }

    return "wibble";
}


date_default_timezone_set("UTC");
db_get_database();

$g_climbtypes = db_get_table("climb_type");
$g_colours    = db_get_table("colour");
$g_grades     = db_get_table("grade");
$g_panels     = db_get_table("panel");
$g_setters    = db_get_table("setter");

$output = html_header ("Craggy Routes");
$output .= "<body>\n";
$output .= "<div class='header'>Craggy Routes</div>\n";
$output .= html_menu();
$output .= "<div class='content'>\n";

$output .= show_main();

$output .= "</div>\n";
$output .= get_errors();
$output .= "</body>\n";
$output .= "</html>";

echo $output;
