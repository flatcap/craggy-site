<?php

include "db.php";
include "utils.php";

function user_date($date)
{
    $d = strtotime($date);
    if ($d !== FALSE)
        $result = strftime("%d %b %Y", $d);
    else
        $result = "";

    return $result;
}

function route_array_verify(&$routes)
{
    $all_valid = TRUE;

    foreach ($routes as $key => &$r) {
        $r['valid'] = TRUE;

        if (!is_numeric ($r['panel']))
            $r['valid'] = FALSE;

        $tmp = parse_colour ($r['colour']);
        if (empty($tmp))
            $r['valid'] = FALSE;
        else
            $r['colour'] = $tmp;

        $tmp = parse_grade ($r['grade']);
        if (empty ($tmp))
            $r['valid'] = FALSE;
        else
            $r['grade'] = $tmp;

        $tmp = parse_setter ($r['setter']);
        if (empty ($tmp))
            $r['valid'] = FALSE;
        else
            $r['setter'] = $tmp;

        $tmp = strtotime($r['date_set']);
        if ($tmp == FALSE)
            $r['valid'] = FALSE;
        else
            $r['date_set'] = strftime("%a %d %b %Y", $tmp);

        $tmp = strtotime($r['date_end']);
        if ($tmp != FALSE)
            $r['date_end'] = strftime("%a %d %b %Y", $tmp);

        if (($r['set'] == 'on') && ($r['valid'] == FALSE))
            $all_valid = FALSE;
    }

    return $all_valid;
}

function route_row_to_array($row)
{
    $r = array();

    $id = get_post_variable("id_{$row}");
    if (empty($id))
        return FALSE;

    $r['id']       = $id;
    $r['panel']    = get_post_variable("panel_{$row}");
    $r['colour']   = get_post_variable("colour_{$row}");
    $r['grade']    = get_post_variable("grade_{$row}");
    $r['notes']    = get_post_variable("notes_{$row}");
    $r['setter']   = get_post_variable("setter_{$row}");
    $r['date_set'] = get_post_variable("date_set_{$row}");
    $r['date_end'] = get_post_variable("date_end_{$row}");
    $r['valid']    = get_post_variable("valid_{$row}");
    $r['set']      = get_post_variable("set_{$row}");

    return $r;
}

function route_form_to_array()
{
    $routes  = array();
    $keys    = array_keys($_POST);
    $matches = array();

    foreach ($keys as $id => $value) {
        preg_match("/(id_)(.*)/", $value, $matches);
        if (empty($matches))
            continue;
        $row = route_row_to_array($matches[2]);
        if ($row === FALSE)
            break;

        array_push($routes, $row);
    }

    return $routes;
}

function route_array_to_form($routes, $readwrite, $checkbox)
{
    if ($readwrite)
        $ro = "";
    else
        $ro = "readonly ";
    $output  = "<table>\n";
    $output .= "<tr>\n";
    $output .= "<th>Panel</th>\n";
    $output .= "<th>Colour</th>\n";
    $output .= "<th>Grade</th>\n";
    $output .= "<th>Notes</th>\n";
    $output .= "<th>Setter</th>\n";
    $output .= "<th>Date Set</th>\n";
    if ($checkbox)
        $output .= "<th>Select</th>\n";
    $output .= "</tr>\n";

    foreach ($routes as $r) {
        if (array_key_exists ("valid", $r) && ($r['valid'] == FALSE) && ($readwrite == TRUE))
            $output .= "<tr class='mand'>\n";
        else
            $output .= "<tr>\n";

        $id = $r['id'];
        $date = user_date($r['date_set']);

        $output .= "<input type='hidden' name='id_{$id}' value='{$r['id']}'>\n";
        $output .= "<td><input {$ro}type='text' size='4' name='panel_{$id}' value='{$r['panel']}'></td>\n";
        $output .= "<td><input {$ro}type='text' size='10' name='colour_{$id}' value='{$r['colour']}'></td>\n";
        $output .= "<td><input {$ro}type='text' size='4' name='grade_{$id}' value='{$r['grade']}'></td>\n";
        $output .= "<td><input {$ro}type='text' size='20' name='notes_{$id}' value='{$r['notes']}'></td>\n";
        $output .= "<td><input {$ro}type='text' size='10' name='setter_{$id}' value='{$r['setter']}'></td>\n";
        $output .= "<td><input {$ro}type='text' size='10' name='date_set_{$id}' value='{$date}'></td>\n";
        if ($checkbox)
            $output .= "<td><input type='checkbox' class='checkbox' name='set_{$id}'></td>\n";
        $output .= "</tr>\n";
    }

    $output .= "</table>\n";

    return $output;
}


function route_delete($routes)
{
    $list = array();
    foreach ($routes as $value) {
        if ($value['set'] == "on")
            array_push ($list, "(id = {$value['id']})");
    }
    $where = "(" . implode (" or ", $list) . ")";

    $result = db_route_delete($where);

    $filtered = array();
    foreach ($routes as $value) {
        if ($value['set'] == "on")
            array_push ($filtered, $value);
    }
    return $filtered;
}

function route_search()
{
    $panel      = get_post_variable('panel');
    $colour     = get_post_variable('colour');
    $grade      = get_post_variable('grade');
    $exactgrade = get_post_variable('exactgrade');
    $notes      = get_post_variable('notes');

    $w = array();
    if (!empty ($panel)) {
        $p = parse_range ($panel);
        $wp = array();
        foreach ($p as $id => $range ) {
            $start = $range['start'];
            $end   = $range['end'];

            if ($start == $end) {
                array_push ($wp, "(panel = {$start})");
            } else {
                array_push ($wp, "(panel >= {$start} and panel <= {$end})");
            }
        }
        array_push ($w, "(" . implode (" or ", $wp) .  ")");
    }

    if (!empty($colour)) {
        array_push ($w, "(colour like '%{$colour}%')");
    }

    $where = implode (" and ", $w);

    $list = db_select ("v_route", "id,panel,colour,grade,notes,setter,date_set,date_end", $where);

    return $list;
}

function route_save_edits($routes)
{
    if (count ($routes) == 0)
        return;

    foreach ($routes as $r) {
        $id       = $r['id'];
        $panel    = get_id_panel ($r['panel']);
        $colour   = get_id_colour ($r['colour']);
        $grade    = get_id_grade ($r['grade']);
        $notes    = $r['notes'];
        $setter   = get_id_setter ($r['setter']);
        $date_set = db_date($r['date_set']);

        $query  = "update route set ";
        $query .= "panel='$panel', colour='$colour', grade='$grade', notes='$notes', setter='$setter', date_set='$date_set' ";
        $query .= "where id='$id';";

        $result = mysql_query($query);
    }

    return $result;
}


function route_parse_new()
{
    $defaults = array ("id" => "",
                       "panel" => "",
                       "colour" => "",
                       "grade" => "",
                       "notes" => "",
                       "setter" => "",
                       "date_set" => "",
                       "valid" => "");

    $date   = get_post_variable('date');
    $setter = get_post_variable('setter');
    $list   = get_post_variable('routes');
    $output = "";
    $count  = 0;

    if (empty($date))
        $date = "today";

    $d = strtotime($date);
    if ($d !== FALSE)
        $defaults['date_set'] = strftime("%a %d %b %Y", $d);

    $defaults['setter'] = parse_setter($setter);

    $routes = parse_routes($list, $defaults);

    return $routes;
}


/**
 * route_form_delete_confirm
 *
 * delete -> route_form_deleted
 */
function route_form_delete_confirm($routes)
{
    $output  = "<h2>Confirm Delete</h2>\n";

    $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
    $output .= "<input type='hidden' name='stage' value='delete_confirm'>\n";
    $output .= route_array_to_form ($routes, FALSE, TRUE);
    $output .= "<br>\n";
    $output .= "<input name='button' accesskey='d' value='Delete' type='submit'>\n";
    $output .= "</form>\n";

    return $output;
}

/**
 * route_form_deleted
 */
function route_form_deleted($routes)
{
    $count = count($routes);

    $output = "<h2>Routes Deleted</h2>\n";

    if ($count > 0) {
        $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
        $output .= route_array_to_form ($routes, FALSE, FALSE);
        $output .= "</form>\n";
    }

    $output .= "{$count} route" . (($count != 1) ? "s" : "") . " deleted.\n";

    return $output;
}

/**
 * route_form_edit_existing
 *
 * verify    -> route_form_edit_existing
 * ok (fail) -> route_form_edit_existing
 * ok (pass) -> route_form_edited
 */
function route_form_edit_existing($routes)
{
    route_array_verify($routes);

    $output  = "<h2>Edit Routes</h2>\n";

    $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
    $output .= "<input type='hidden' name='stage' value='edit_routes'>\n";
    $output .= route_array_to_form($routes, TRUE, FALSE);
    $output .= "<br>\n";
    $output .= "<input name='button' accesskey='v' value='Verify' type='submit'>\n";
    $output .= "<input name='button' accesskey='s' value='Save' type='submit'>\n";
    $output .= "</form>\n";

    return $output;
}

/**
 * route_form_edited
 */
function route_form_edited($routes)
{
    $count = count($routes);

    $output  = "<h2>Routes Saved</h2>\n";

    if ($count > 0) {
        $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
        $output .= "<input type='hidden' name='stage' value='routes_edited'>\n";
        $output .= route_array_to_form ($routes, FALSE, FALSE);
        $output .= "</form>\n";
    }

    $output .= "{$count} route" . (($count != 1) ? "s" : "") . " saved.\n";

    return $output;
}

/**
 * route_form_search
 *
 * search -> route_form_search_results
 * edit   -> route_form_edit_existing
 * delete -> route_form_delete_confirm
 */
function route_form_search()
{
    $output  = "<h2>Route Search</h2>\n";

    $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
    $output .= "  <input type='hidden' name='stage' value='search'>\n";
    $output .= "  <label for='panel' accesskey='p'><u>P</u>anel</label>\n";
    $output .= "  <input type='text' name='panel' id='panel' value=''>\n";
    $output .= "  <br>\n";
    $output .= "  <label for='colour' accesskey='c'><u>C</u>olour</label>\n";
    $output .= "  <input type='text' name='colour' id='colour' value=''>\n";
    $output .= "  <br>\n";
    $output .= "  <label for='grade' accesskey='g'><u>G</u>rade</label>\n";
    $output .= "  <input type='text' name='grade' id='grade' value=''>\n";
    $output .= "  <br>\n";
    $output .= "  <label for='exactgrade' accesskey='x'>E<u>x</u>act Grade</label>\n";
    $output .= "  <input type='checkbox' class='checkbox' name='exactgrade' id='exactgrade'>\n";
    $output .= "  <br>\n";
    $output .= "  <label for='notes' accesskey='n'><u>N</u>otes</label>\n";
    $output .= "  <input type='text' name='notes' id='notes' value=''>\n";
    $output .= "  <br>\n";
    $output .= "  <br>\n";
    $output .= "  <input name='button' accesskey='s' value='Search' type='submit' class='default'>\n";
    $output .= "  <input name='button' accesskey='e' value='Edit'   type='submit'>\n";
    $output .= "  <input name='button' accesskey='d' value='Delete' type='submit'>\n";
    $output .= "</form>\n";

    $output .= html_set_focus ("panel");

    return $output;
}

/**
 * route_form_search_results
 *
 * edit   -> route_form_edit_existing
 * delete -> route_form_delete_confirm
 */
function route_form_search_results($routes)
{
    $output  = "<h2>Search Results</h2>\n";

    $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
    $output .= "<input type='hidden' name='stage' value='search_results'>\n";

    $output .= route_array_to_form ($routes, FALSE, FALSE);

    $output .= "<br>\n";
    $output .= "<input name='button' accesskey='e' value='Edit' type='submit'>\n";
    $output .= "<input name='button' accesskey='d' value='Delete' type='submit'>\n";
    $output .= "</form>\n";

    return $output;
}


function route_addnew_commit($routes)
{
    $output = "";

    //commit to database
    $success = db_route_add($routes);

    // generate new form
    $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
    $output .= "<input type='hidden' name='stage' value='2'>\n";
    $output .= route_array_to_form($routes, TRUE, FALSE);
    $output .= "</form>";

    return $output;
}

function route_addnew()
{
    $output = '';
    $stage = get_post_variable('stage');
    $stage++;

    switch ($stage) {
        case 2:
            $output .= route_addnew_verify();
            break;
        case 3:
            $button = get_post_variable('button');
            $routes = route_form_to_array();
            // verify contents
            $valid = route_array_verify($routes);
            if ($valid && ($button == "OK")) {
                $output .= route_addnew_commit($routes);
            } else {
                $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
                $output .= "<input type='hidden' name='stage' value='2'>\n";
                $output .= route_array_to_form($routes, TRUE, FALSE);
                $output .= "<br>";
                $output .= "<input name='button' accesskey='v' value='Verify' type='submit'>";
                $output .= "<input name='button' accesskey='o' value='OK' type='submit'>";
                $output .= "</form>";

            }
            break;
        case 1:
        default:
            $output .= route_addnew_annotation();
            break;
    }

    return $output;
}


function route_addnew_annotation()
{
    $output  = "<h2>Add New Routes</h2>\n";

    $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
    $output .= "  <input type='hidden' name='stage' value='new_annotation'>\n";
    $output .= "  <label for='date' accesskey='d'><u>D</u>ate</label>\n";
    $output .= "  <input type='text' name='date' id='date' value=''>\n";
    $output .= "  <br>\n";
    $output .= "  <label for='setter' accesskey='s'><u>S</u>etter</label>\n";
    $output .= "  <input type='text' name='setter' id='setter' value=''>\n";
    $output .= "  <br>\n";
    $output .= "  <label for='routes' accesskey='r'><u>R</u>outes</label>\n";
    $output .= "  <textarea rows='10' name='routes' id='routes'></textarea>\n";
    $output .= "  <br>\n";
    $output .= "  <input name='button' accesskey='a' value='Add' type='submit'>\n";
    $output .= "</form>\n";

    return $output;
}

function route_form_edit_new($routes)
{
    $output  = "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
    $output .= "<input type='hidden' name='stage' value='2'>\n";
    $output .= route_array_to_form($routes, TRUE, FALSE);
    $output .= "<br>";
    $output .= "<input name='button' accesskey='v' value='Verify' type='submit'>";
    $output .= "<input name='button' accesskey='o' value='OK' type='submit'>";
    $output .= "</form>";

    return $output;
}


function route_main()
{
    $stage  = get_post_variable('stage');
    $button = get_post_variable('button');
    $output = "";

    switch ($stage) {
        case "search":
            $routes = route_search();
            if ($button == "Edit")
                $output .= route_form_edit_existing($routes);
            else if ($button == "Delete")
                $output .= route_form_delete_confirm($routes);
            else
                $output .= route_form_search_results($routes);
            break;
        case "search_results":
            $routes = route_form_to_array();
            if ($button == "Edit")
                $output .= route_form_edit_existing($routes);
            else if ($button == "Delete")
                $output .= route_form_delete_confirm($routes);
            break;
        case "edit_routes";
            $routes = route_form_to_array();
            $valid = route_array_verify($routes);
            if (($button == "Save") && ($valid == TRUE)) {
                $result = route_save_edits($routes);
                $output .= route_form_edited($routes);
            } else {
                $output .= route_form_edit_existing($routes);
            }
            break;
        case "delete_confirm":
            if ($button == "Delete") {
                $routes = route_form_to_array();
                $routes = route_delete($routes);
                $output .= route_form_deleted($routes);
            }
            break;
        case "new_annotation":
            $routes = route_parse_new();
            $output .= route_form_edit_new($routes);
            break;
        default:
            $output .= route_form_search();
            $output .= "<br><br>";
            $output .= route_addnew_annotation();
            break;
    }

    return $output;
}


date_default_timezone_set("UTC");
db_get_database();

$g_colours = db_get_table("colour");
$g_grades  = db_get_table("grade");
$g_panels  = db_get_table("panel");
$g_setters = db_get_table("setter");

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
