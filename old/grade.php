<?php

include 'db.php';
include 'utils.php';

$g_results = array();

/* Helpers */

function grade_hash($row)
{
    $text = "{$row['id']}|{$row['order']}|{$row['grade']}";

    return hash ('md5', $text);
}

function grade_array_verify(&$grades)
{
    $all_valid = true;

    foreach ($grades as $key => &$r) {
        $r['valid'] = true;

        if (!is_numeric ($r['order']))
            $r['valid'] = false;
        if ($r['order'] < 1)
            $r['valid'] = false;

        if (!$r['valid'])
            $all_valid = false;
    }

    return $all_valid;
}

function grade_row_to_array($row)
{
    $id = get_post_variable("id_{$row}");
    if (empty($id))
        return false;

    $r = array();

    $r['id']    = $id;
    $r['grade'] = get_post_variable("grade_{$row}");
    $r['hash']  = get_post_variable("hash_{$row}");
    $r['order'] = get_post_variable("order_{$row}");
    $r['set']   = get_post_variable("set_{$row}");

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
        if ($row === false)
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
    if ($checkbox)
        $output .= "<th>&#10004;</th>";     // A bold tick
    $output .= "<th>Order</th>";
    $output .= "<th>Grade</th>";
    //$output .= "<th>Hash</th>";
    $output .= "</tr>\n";

    foreach ($grades as $g) {
        if (array_key_exists ("valid", $g) && ($g['valid'] == false) && ($readwrite == true))
            $output .= "<tr class='mand'>\n";
        else
            $output .= "<tr>\n";

        $id    = $g['id'];
        $order = $g['order'];
        $grade = $g['grade'];
        $hash  = $g['hash'];

        $output .= "<input type='hidden' name='id_{$id}' value='{$id}'>\n";
        $output .= "<input type='hidden' name='hash_{$id}' value='{$hash}'>\n";
        if ($checkbox)
            $output .= "<td><input type='checkbox' class='checkbox' name='set_{$id}'></td>\n";
        if ($readwrite) {
            $output .= "<td><input {$ro}type='text' size='6' name='order_{$id}' value='{$order}'></td>\n";
            $output .= "<td><input {$ro}type='text' size='4' name='grade_{$id}' value='{$grade}'></td>\n";
        } else {
            $output .= "<td>{$order}</td>\n";
            $output .= "<td>{$grade}</td>\n";
        }
        //$output .= "<td>{$hash}</td>\n";

        $output .= "</tr>\n";
    }

    $output .= "</table>\n";

    return $output;
}


/* Database */
// add debug_database variable

function grade_get_grades($hash = false)
{
    $grades = db_select("grade", null, null, "grade.order");
    if ($hash) {
        foreach ($grades as &$g) {
            $g['hash'] = grade_hash($g);
        }
    }

    return $grades;
}

function grade_row_add($row)
{
    $o = $row['order'];
    $g = $row['grade'];

    if (empty ($o) || empty ($g))
        return false;

    $query  = "insert into grade (grade.order,grade) VALUES ";
    $query .= "('{$o}', '{$g}');";

    //var_dump ($query);
    $result = mysql_query($query);
    //var_dump ($result);

    return $result;
}

function grade_row_edit($g)
{
    $id    = $g['id'];
    $order = $g['order'];
    $grade = $g['grade'];

    $query  = "update grade set ";
    $query .= "grade.order='$order', grade.grade='$grade' ";
    $query .= "where id='$id';";

    //var_dump ($query);
    $result = mysql_query($query);
    //var_dump ($result);

    return $result;
}

function grade_row_delete($id)
{
    $query  = "delete from grade where id = $id;";

    //var_dump ($query);
    $result = mysql_query($query);
    //var_dump ($result);

    return $result;
}


/* Pages */

function grade_list()
{
    $grades = grade_get_grades(true);
    $output = "";
    $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
    $output .= "<input type='hidden' name='stage' value='grade_list'>\n";

    $output .= grade_array_to_form ($grades, false, false);

    $output .= "<br>\n";
    $output .= "<input name='button' accesskey='a' value='Add' type='submit'>\n";
    $output .= "<input name='button' accesskey='e' value='Edit' type='submit'>\n";
    $output .= "<input name='button' accesskey='d' value='Delete' type='submit'>\n";
    $output .= "</form>\n";
    return $output;
}

function grade_add()
{
    global $g_results;
    $output = "";
    $button = get_post_variable('button');

    if ($button == "OK") {
        $grades = grade_form_to_array();
        $count = 0;
        foreach ($grades as $row) {
            if (grade_row_add ($row))
                $count++;
        }
        if ($count > 0)
            array_push ($g_results, "Created {$count} grade" . (($count != 1) ? "s" : ""));
        $output .= grade_list();
    } else {
        $grades = array();
        for ($i = 1; $i <= 8; $i++) {
            $g = array();
            $g['id'] = $i;
            $g['order'] = "";
            $g['grade'] = "";
            $g['hash'] = "";
            $grades[$i] = $g;
        }

        $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
        $output .= "<input type='hidden' name='stage' value='grade_add'>\n";

        $output .= grade_array_to_form ($grades, true, false);

        $output .= "<br>\n";
        $output .= "<input name='button' accesskey='o' value='OK' type='submit'>\n";
        $output .= "<input name='button' accesskey='c' value='Cancel' type='submit'>\n";
        $output .= "</form>\n";
    }

    return $output;
}

function grade_edit_commit (&$grades)
{
    global $g_results;
    $count = 0;

    foreach ($grades as $g) {
        $orig = $g['hash'];
        $curr = grade_hash ($g);
        if ($orig != $curr) {
            grade_row_edit($g);
            $count++;
        }
    }

    if ($count > 0)
        array_push ($g_results, "Updated {$count} grade" . (($count != 1) ? "s" : ""));
}

function grade_edit($grades)
{
    $output = "";
    $valid  = grade_array_verify($grades);
    $button = get_post_variable('button');

    if ($valid && ($button == "OK")) {
        grade_edit_commit($grades);
        $output .= grade_list();
    } else {
        $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
        $output .= "<input type='hidden' name='stage' value='grade_edit'>\n";

        $output .= grade_array_to_form ($grades, true, false);

        $output .= "<br>\n";
        $output .= "<input name='button' accesskey='o' value='OK' type='submit' class='default'>\n";
        $output .= "<input name='button' accesskey='c' value='Cancel' type='submit'>\n";
        $output .= "</form>\n";
    }

    return $output;
}

function grade_delete_commit($grades)
{
    global $g_results;
    $count = 0;

    foreach ($grades as $g) {
        if ($g['set'] == "on") {
            grade_row_delete ($g['id']);
            $count++;
        }
    }

    if ($count > 0)
        array_push ($g_results, "Deleted {$count} grade" . (($count != 1) ? "s" : ""));
}

function grade_delete($grades)
{
    $output = "";
    $button = get_post_variable('button');

    if ($button == "OK") {
        grade_delete_commit($grades);
        $output .= grade_list();
    } else {
        $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
        $output .= "<input type='hidden' name='stage' value='grade_delete'>\n";

        $output .= grade_array_to_form ($grades, false, true);

        $output .= "<br>\n";
        $output .= "<input name='button' accesskey='o' value='OK' type='submit' class='default'>\n";
        $output .= "<input name='button' accesskey='c' value='Cancel' type='submit'>\n";
        $output .= "</form>\n";
    }

    return $output;
}


function grade_main()
{
    $output = "";
    $stage  = get_post_variable('stage');
    $button = get_post_variable('button');

    if (empty($stage) || ($button == "Cancel"))
        $stage = "grade_list";

    // Create the page
    switch ($stage) {
        case "grade_delete":
            $grades = grade_form_to_array();
            $output .= grade_delete ($grades);
            break;
        case "grade_edit":
            $grades = grade_form_to_array();
            $output .= grade_edit ($grades);
            break;
        case "grade_add":
            $output .= grade_add($button);
            break;
        case "grade_list":
        default:
            if ($button == "Add") {
                $output .= grade_add();
            } else if ($button == "Edit") {
                $grades = grade_get_grades(true);
                $output .= grade_edit($grades);
            } else if ($button == "Delete") {
                $grades = grade_get_grades(true);
                $output .= grade_delete($grades);
            } else {
                $output .= grade_list();
            }
            break;
    }

    return $output;
}


// Global string: "Operation did this..."

date_default_timezone_set('UTC');
db_get_database();

$output = html_header ("Craggy Grades");
$output .= "<body>\n";
$output .= "<div class='header'>Craggy Grades</div>\n";
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

$output .= grade_main();

$output .= "</div>\n";
$output .= get_errors();
$output .= "</body>\n";
$output .= "</html>";

echo $output;
