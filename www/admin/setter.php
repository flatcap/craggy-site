<?php

include "../db.php";
include "utils.php";

$g_results = array();

/* Helpers */

function setter_hash($row)
{
    $text = "{$row['id']}|{$row['initials']}|{$row['name']}";

    return hash ('md5', $text);
}

function setter_array_verify(&$setters)
{
    $all_valid = TRUE;

    return $all_valid;
}

function setter_row_to_array($row)
{
    $id = get_post_variable("id_{$row}");
    if (empty($id))
        return FALSE;

    $r = array();

    $r['id']       = $id;
    $r['hash']     = get_post_variable("hash_{$row}");
    $r['initials'] = get_post_variable("initials_{$row}");
    $r['name']     = get_post_variable("name_{$row}");
    $r['set']      = get_post_variable("set_{$row}");

    return $r;
}

function setter_form_to_array()
{
    $setters  = array();
    $keys    = array_keys($_POST);
    $matches = array();

    foreach ($keys as $id => $value) {
        preg_match("/(id_)(.*)/", $value, $matches);
        if (empty($matches))
            continue;
        $row = setter_row_to_array($matches[2]);
        if ($row === FALSE)
            break;

        array_push($setters, $row);
    }

    return $setters;
}

function setter_array_to_form($setters, $readwrite, $checkbox)
{
    if ($readwrite)
        $ro = "";
    else
        $ro = "readonly ";
    $output  = "<table>\n";
    $output .= "<tr>\n";
    if ($checkbox)
        $output .= "<th>&#10004;</th>";     // A bold tick
    $output .= "<th>Initials</th>";
    $output .= "<th>Name</th>";
    //$output .= "<th>Hash</th>";
    $output .= "</tr>\n";

    foreach ($setters as $g) {
        if (array_key_exists ("valid", $g) && ($g['valid'] == FALSE) && ($readwrite == TRUE))
            $output .= "<tr class='mand'>\n";
        else
            $output .= "<tr>\n";

        $id       = $g['id'];
        $initials = $g['initials'];
        $name     = $g['name'];
        $hash     = $g['hash'];

        $output .= "<input type='hidden' name='id_{$id}' value='{$id}'>\n";
        $output .= "<input type='hidden' name='hash_{$id}' value='{$hash}'>\n";
        if ($checkbox)
            $output .= "<td><input type='checkbox' class='checkbox' name='set_{$id}'></td>\n";
        if ($readwrite) {
            $output .= "<td><input {$ro}type='text' size='4' name='initials_{$id}' value='{$initials}'></td>\n";
            $output .= "<td><input {$ro}type='text' size='6' name='name_{$id}' value='{$name}'></td>\n";
        } else {
            $output .= "<td>{$initials}</td>\n";
            $output .= "<td>{$name}</td>\n";
        }
        //$output .= "<td>{$hash}</td>\n";

        $output .= "</tr>\n";
    }

    $output .= "</table>\n";

    return $output;
}


/* Database */
// add debug_database variable

function setter_get_setters($hash = FALSE)
{
    $setters = db_select("setter", NULL, NULL, "name");
    if ($hash) {
        foreach ($setters as &$g) {
            $g['hash'] = setter_hash($g);
        }
    }

    return $setters;
}

function setter_row_add($row)
{
    $initials  = $row['initials'];
    $name = $row['name'];

    if (empty ($initials) || empty ($name))
        return FALSE;

    $initials = strtolower ($initials);

    $query  = "insert into setter (initials,name) VALUES ";
    $query .= "('{$initials}', '{$name}');";

    //var_dump ($query);
    $result = mysql_query($query);
    //var_dump ($result);

    return $result;
}

function setter_row_edit($g)
{
    $id     = $g['id'];
    $initials   = $g['initials'];
    $name = $g['name'];

    $initials = strtolower ($initials);
    $initials = str_replace (" ", "", $initials);

    $query  = "update setter set ";
    $query .= "setter.initials='$initials', setter.name='$name' ";
    $query .= "where id='$id';";

    //var_dump ($query);
    $result = mysql_query($query);
    //var_dump ($result);

    return $result;
}

function setter_row_delete($id)
{
    $query  = "delete from setter where id = $id;";

    //var_dump ($query);
    $result = mysql_query($query);
    //var_dump ($result);

    return $result;
}


/* Pages */

function setter_list()
{
    $setters = setter_get_setters(TRUE);
    $output = "";
    $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
    $output .= "<input type='hidden' name='stage' value='setter_list'>\n";

    $output .= setter_array_to_form ($setters, FALSE, FALSE);

    $output .= "<br>\n";
    $output .= "<input name='button' accesskey='a' value='Add' type='submit'>\n";
    $output .= "<input name='button' accesskey='e' value='Edit' type='submit'>\n";
    $output .= "<input name='button' accesskey='d' value='Delete' type='submit'>\n";
    $output .= "</form>\n";
    return $output;
}

function setter_add()
{
    global $g_results;
    $output = "";
    $button = get_post_variable('button');

    if ($button == "OK") {
        $setters = setter_form_to_array();
        $count = 0;
        foreach ($setters as $row) {
            if (setter_row_add ($row))
                $count++;
        }
        if ($count > 0)
            array_push ($g_results, "Created {$count} setter" . (($count != 1) ? "s" : ""));
        $output .= setter_list();
    } else {
        $setters = array();
        for ($i = 1; $i <= 8; $i++) {
            $g = array();
            $g['id'] = $i;
            $g['initials'] = "";
            $g['name'] = "";
            $g['hash'] = "";
            $setters[$i] = $g;
        }

        $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
        $output .= "<input type='hidden' name='stage' value='setter_add'>\n";

        $output .= setter_array_to_form ($setters, TRUE, FALSE);

        $output .= "<br>\n";
        $output .= "<input name='button' accesskey='o' value='OK' type='submit'>\n";
        $output .= "<input name='button' accesskey='c' value='Cancel' type='submit'>\n";
        $output .= "</form>\n";
    }

    return $output;
}

function setter_edit_commit (&$setters)
{
    global $g_results;
    $count = 0;

    foreach ($setters as $g) {
        $orig = $g['hash'];
        $curr = setter_hash ($g);
        if ($orig != $curr) {
            setter_row_edit($g);
            $count++;
        }
    }

    if ($count > 0)
        array_push ($g_results, "Updated {$count} setter" . (($count != 1) ? "s" : ""));
}

function setter_edit($setters)
{
    $output = "";
    $valid  = setter_array_verify($setters);
    $button = get_post_variable('button');

    if ($valid && ($button == "OK")) {
        setter_edit_commit($setters);
        $output .= setter_list();
    } else {
        $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
        $output .= "<input type='hidden' name='stage' value='setter_edit'>\n";

        $output .= setter_array_to_form ($setters, TRUE, FALSE);

        $output .= "<br>\n";
        $output .= "<input name='button' accesskey='o' value='OK' type='submit' class='default'>\n";
        $output .= "<input name='button' accesskey='c' value='Cancel' type='submit'>\n";
        $output .= "</form>\n";
    }

    return $output;
}

function setter_delete_commit($setters)
{
    global $g_results;
    $count = 0;

    foreach ($setters as $g) {
        if ($g['set'] == "on") {
            setter_row_delete ($g['id']);
            $count++;
        }
    }

    if ($count > 0)
        array_push ($g_results, "Deleted {$count} setter" . (($count != 1) ? "s" : ""));
}

function setter_delete($setters)
{
    $output = "";
    $button = get_post_variable('button');

    if ($button == "OK") {
        setter_delete_commit($setters);
        $output .= setter_list();
    } else {
        $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
        $output .= "<input type='hidden' name='stage' value='setter_delete'>\n";

        $output .= setter_array_to_form ($setters, FALSE, TRUE);

        $output .= "<br>\n";
        $output .= "<input name='button' accesskey='o' value='OK' type='submit' class='default'>\n";
        $output .= "<input name='button' accesskey='c' value='Cancel' type='submit'>\n";
        $output .= "</form>\n";
    }

    return $output;
}


function setter_main()
{
    $output = "";
    $stage  = get_post_variable('stage');
    $button = get_post_variable('button');

    if (empty($stage) || ($button == "Cancel"))
        $stage = "setter_list";

    // Create the page
    switch ($stage) {
        case "setter_delete":
            $setters = setter_form_to_array();
            $output .= setter_delete ($setters);
            break;
        case "setter_edit":
            $setters = setter_form_to_array();
            $output .= setter_edit ($setters);
            break;
        case "setter_add":
            $output .= setter_add($button);
            break;
        case "setter_list":
        default:
            if ($button == "Add") {
                $output .= setter_add();
            } else if ($button == "Edit") {
                $setters = setter_get_setters(TRUE);
                $output .= setter_edit($setters);
            } else if ($button == "Delete") {
                $setters = setter_get_setters(TRUE);
                $output .= setter_delete($setters);
            } else {
                $output .= setter_list();
            }
            break;
    }

    return $output;
}


// Global string: "Operation did this..."

date_default_timezone_set("UTC");
db_get_database();

$output = html_header ("Craggy Setters");
$output .= "<body>\n";
$output .= "<div class='header'>Craggy Setters</div>\n";
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

$output .= setter_main();

$output .= "</div>\n";
$output .= get_errors();
$output .= "</body>\n";
$output .= "</html>";

echo $output;
