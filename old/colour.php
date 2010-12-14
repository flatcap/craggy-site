<?php

include "db.php";
include "utils.php";

$g_results = array();

/* Helpers */

function colour_hash($row)
{
    $text = "{$row['id']}|{$row['colour']}|{$row['abbr']}";

    return hash ('md5', $text);
}

function colour_array_verify(&$colours)
{
    $all_valid = TRUE;

    return $all_valid;
}

function colour_row_to_array($row)
{
    $id = get_post_variable("id_{$row}");
    if (empty($id))
        return FALSE;

    $r = array();

    $r['id']     = $id;
    $r['abbr']   = get_post_variable("abbr_{$row}");
    $r['colour'] = get_post_variable("colour_{$row}");
    $r['hash']   = get_post_variable("hash_{$row}");
    $r['set']    = get_post_variable("set_{$row}");

    return $r;
}

function colour_form_to_array()
{
    $colours  = array();
    $keys    = array_keys($_POST);
    $matches = array();

    foreach ($keys as $id => $value) {
        preg_match("/(id_)(.*)/", $value, $matches);
        if (empty($matches))
            continue;
        $row = colour_row_to_array($matches[2]);
        if ($row === FALSE)
            break;

        array_push($colours, $row);
    }

    return $colours;
}

function colour_array_to_form($colours, $readwrite, $checkbox)
{
    if ($readwrite)
        $ro = "";
    else
        $ro = "readonly ";
    $output  = "<table>\n";
    $output .= "<tr>\n";
    if ($checkbox)
        $output .= "<th>&#10004;</th>";     // A bold tick
    $output .= "<th>Colour</th>";
    $output .= "<th>Abbr</th>";
    //$output .= "<th>Hash</th>";
    $output .= "</tr>\n";

    foreach ($colours as $g) {
        if (array_key_exists ("valid", $g) && ($g['valid'] == FALSE) && ($readwrite == TRUE))
            $output .= "<tr class='mand'>\n";
        else
            $output .= "<tr>\n";

        $id     = $g['id'];
        $abbr   = $g['abbr'];
        $colour = $g['colour'];
        $hash   = $g['hash'];

        $output .= "<input type='hidden' name='id_{$id}' value='{$id}'>\n";
        $output .= "<input type='hidden' name='hash_{$id}' value='{$hash}'>\n";
        if ($checkbox)
            $output .= "<td><input type='checkbox' class='checkbox' name='set_{$id}'></td>\n";
        if ($readwrite) {
            $output .= "<td><input {$ro}type='text' size='4' name='colour_{$id}' value='{$colour}'></td>\n";
            $output .= "<td><input {$ro}type='text' size='6' name='abbr_{$id}' value='{$abbr}'></td>\n";
        } else {
            $output .= "<td>{$colour}</td>\n";
            $output .= "<td>{$abbr}</td>\n";
        }
        //$output .= "<td>{$hash}</td>\n";

        $output .= "</tr>\n";
    }

    $output .= "</table>\n";

    return $output;
}


/* Database */
// add debug_database variable

function colour_get_colours($hash = FALSE)
{
    $colours = db_select("colour", NULL, NULL, "colour");
    if ($hash) {
        foreach ($colours as &$g) {
            $g['hash'] = colour_hash($g);
        }
    }

    return $colours;
}

function colour_row_add($row)
{
    $abbr  = $row['abbr'];
    $grade = $row['colour'];

    if (empty ($abbr) || empty ($grade))
        return FALSE;

    $abbr = strtolower ($abbr);
    $abbr = str_replace (" ", "", $abbr);

    $query  = "insert into colour (abbr,colour) VALUES ";
    $query .= "('{$abbr}', '{$grade}');";

    //var_dump ($query);
    $result = mysql_query($query);
    //var_dump ($result);

    return $result;
}

function colour_row_edit($g)
{
    $id     = $g['id'];
    $abbr   = $g['abbr'];
    $colour = $g['colour'];

    $abbr = strtolower ($abbr);
    $abbr = str_replace (" ", "", $abbr);

    $query  = "update colour set ";
    $query .= "colour.abbr='$abbr', colour.colour='$colour' ";
    $query .= "where id='$id';";

    //var_dump ($query);
    $result = mysql_query($query);
    //var_dump ($result);

    return $result;
}

function colour_row_delete($id)
{
    $query  = "delete from colour where id = $id;";

    //var_dump ($query);
    $result = mysql_query($query);
    //var_dump ($result);

    return $result;
}


/* Pages */

function colour_list()
{
    $colours = colour_get_colours(TRUE);
    $output = "";
    $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
    $output .= "<input type='hidden' name='stage' value='colour_list'>\n";

    $output .= colour_array_to_form ($colours, FALSE, FALSE);

    $output .= "<br>\n";
    $output .= "<input name='button' accesskey='a' value='Add' type='submit'>\n";
    $output .= "<input name='button' accesskey='e' value='Edit' type='submit'>\n";
    $output .= "<input name='button' accesskey='d' value='Delete' type='submit'>\n";
    $output .= "</form>\n";
    return $output;
}

function colour_add()
{
    global $g_results;
    $output = "";
    $button = get_post_variable('button');

    if ($button == "OK") {
        $colours = colour_form_to_array();
        $count = 0;
        foreach ($colours as $row) {
            if (colour_row_add ($row))
                $count++;
        }
        if ($count > 0)
            array_push ($g_results, "Created {$count} colour" . (($count != 1) ? "s" : ""));
        $output .= colour_list();
    } else {
        $colours = array();
        for ($i = 1; $i <= 8; $i++) {
            $g = array();
            $g['id'] = $i;
            $g['abbr'] = "";
            $g['colour'] = "";
            $g['hash'] = "";
            $colours[$i] = $g;
        }

        $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
        $output .= "<input type='hidden' name='stage' value='colour_add'>\n";

        $output .= colour_array_to_form ($colours, TRUE, FALSE);

        $output .= "<br>\n";
        $output .= "<input name='button' accesskey='o' value='OK' type='submit'>\n";
        $output .= "<input name='button' accesskey='c' value='Cancel' type='submit'>\n";
        $output .= "</form>\n";
    }

    return $output;
}

function colour_edit_commit (&$colours)
{
    global $g_results;
    $count = 0;

    foreach ($colours as $g) {
        $orig = $g['hash'];
        $curr = colour_hash ($g);
        if ($orig != $curr) {
            colour_row_edit($g);
            $count++;
        }
    }

    if ($count > 0)
        array_push ($g_results, "Updated {$count} colour" . (($count != 1) ? "s" : ""));
}

function colour_edit($colours)
{
    $output = "";
    $valid  = colour_array_verify($colours);
    $button = get_post_variable('button');

    if ($valid && ($button == "OK")) {
        colour_edit_commit($colours);
        $output .= colour_list();
    } else {
        $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
        $output .= "<input type='hidden' name='stage' value='colour_edit'>\n";

        $output .= colour_array_to_form ($colours, TRUE, FALSE);

        $output .= "<br>\n";
        $output .= "<input name='button' accesskey='o' value='OK' type='submit' class='default'>\n";
        $output .= "<input name='button' accesskey='c' value='Cancel' type='submit'>\n";
        $output .= "</form>\n";
    }

    return $output;
}

function colour_delete_commit($colours)
{
    global $g_results;
    $count = 0;

    foreach ($colours as $g) {
        if ($g['set'] == "on") {
            colour_row_delete ($g['id']);
            $count++;
        }
    }

    if ($count > 0)
        array_push ($g_results, "Deleted {$count} colour" . (($count != 1) ? "s" : ""));
}

function colour_delete($colours)
{
    $output = "";
    $button = get_post_variable('button');

    if ($button == "OK") {
        colour_delete_commit($colours);
        $output .= colour_list();
    } else {
        $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
        $output .= "<input type='hidden' name='stage' value='colour_delete'>\n";

        $output .= colour_array_to_form ($colours, FALSE, TRUE);

        $output .= "<br>\n";
        $output .= "<input name='button' accesskey='o' value='OK' type='submit' class='default'>\n";
        $output .= "<input name='button' accesskey='c' value='Cancel' type='submit'>\n";
        $output .= "</form>\n";
    }

    return $output;
}


function colour_main()
{
    $output = "";
    $stage  = get_post_variable('stage');
    $button = get_post_variable('button');

    if (empty($stage) || ($button == "Cancel"))
        $stage = "colour_list";

    // Create the page
    switch ($stage) {
        case "colour_delete":
            $colours = colour_form_to_array();
            $output .= colour_delete ($colours);
            break;
        case "colour_edit":
            $colours = colour_form_to_array();
            $output .= colour_edit ($colours);
            break;
        case "colour_add":
            $output .= colour_add($button);
            break;
        case "colour_list":
        default:
            if ($button == "Add") {
                $output .= colour_add();
            } else if ($button == "Edit") {
                $colours = colour_get_colours(TRUE);
                $output .= colour_edit($colours);
            } else if ($button == "Delete") {
                $colours = colour_get_colours(TRUE);
                $output .= colour_delete($colours);
            } else {
                $output .= colour_list();
            }
            break;
    }

    return $output;
}


// Global string: "Operation did this..."

date_default_timezone_set("UTC");
db_get_database();

$output = html_header ("Craggy Colours");
$output .= "<body>\n";
$output .= "<div class='header'>Craggy Colours</div>\n";
$output .= html_menu();
if (count ($g_results)) {
    $output .= "<div class='results'>\n";
    $output .= "<ul>\n";
    foreach ($g_results as $r) {
        $output .= "<li>{$s}</li>\n";
    }
    $output .= "</ul>\n";
    $output .= "</div>\n";
}
$output .= "<div class='content'>\n";

$output .= colour_main();

$output .= "</div>\n";
$output .= get_errors();
$output .= "</body>\n";
$output .= "</html>";

echo $output;
