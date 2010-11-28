<?php

define ("SORT_ROUTE_PANEL",     1);
define ("SORT_ROUTE_GRADE",     2);
define ("SORT_ROUTE_CHECKLIST", 3);
define ("SORT_ROUTE_AGE",       4);

/* TODO
 *
 * General
 *     Sort grades by "order"
 *     Filter out routes by date
 *     Add climbs
 *     Select view date
 *
 * UI
 *     Display: Panels, routes, climbs, stats
 *     Sort: Panels, routes, climbs
 *
 * Actions
 *     Add route
 *     Delete route
 */

function get_database()
{
    $db = mysql_connect('localhost', 'root', 'jilkerm');
    if (!$db) {
        die('Could not connect: ' . mysql_error());
    }
    mysql_select_db("craggy");
    return $db;
}

function get_table($name)
{
    $result = mysql_query("select * from $name;");

    $list = array();
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $list[$row['id']] = $row;
    }

    mysql_free_result($result);
    return $list;
}

function html_header()
{
    echo '<html><head><title>wibble</title>' . "\n";
    echo '<meta http-equiv="Refresh" content="1;url=index.php">' . "\n";
}

function main()
{
    $db = get_database();

    $table_climb_type = get_table("climb_type");
    $table_colour     = get_table("colour");
    $table_grade      = get_table("grade");
    $table_panel      = get_table("panel");
    $table_setter     = get_table("setter");
    $table_route      = get_table("route");

    $routes = array();
    foreach ($table_route as $row) {
        $route = array();

        $route['panel']    = &$table_panel[$row['panel']];
        $route['type']     = &$table_climb_type[$table_panel[$row['panel']]['type']];
        $route['colour']   = &$table_colour[$row['colour']];
        $route['grade']    = &$table_grade[$row['grade']];
        $route['setter']   = &$table_setter[$row['setter']];
        $route['notes']    = $row['notes'];
        $route['date_set'] = $row['date_set'];
        $route['date_end'] = $row['date_end'];

        array_push ($routes, $route);
    }

    //usort($routes, "cmp_panel");
    //usort($routes, "cmp_grade");
    //usort($routes, "cmp_checklist");
    //usort($routes, "cmp_age");

    foreach ($table_climb_type as $id) {
        $table_climb_type[$id['id']]['count'] = 0;
    }

    foreach ($table_grade as $id) {
        $table_grade[$id['id']]['count'] = 0;
    }

    foreach ($table_grade as $id) {
        $table_setter[$id['id']]['count'] = 0;
    }

    $block = "";
    $today = strtotime("today");
    $count = 0;
    echo "<table border='1' cellpadding='3' cellspacing='0'>";
    printf ("<tr>");
    printf ("<td>Panel</td>");
    printf ("<td>Type</td>");
    printf ("<td>Colour</td>");
    printf ("<td>Grade</td>");
    printf ("<td>Notes</td>");
    printf ("<td>Setter</td>");
    printf ("<td>Date</td>");
    printf ("<td>End</td>");
    printf ("<td>Age</td>");
    printf ("<td>Months</td>");
    printf ("</tr>");
    foreach ($routes as $id => $route) {

        $p = $route['panel']['number'];
        $t = $route['type']['type'];
        $c = $route['colour']['colour'];
        $g = $route['grade']['grade'];
        $s = $route['setter']['name'];
        $n = $route['notes'];
        $d = $route['date_set'];
        $e = $route['date_end'];

        if (empty($d)) {
            $a = "";
            $m = "";
        } else {
            $a = floor (($today - strtotime($d)) / 86400);
            $m = floor ($a / 30.5);
        }

        /*
        $b = grade_block($g);
        if ($block <> $b) {
            $block = $b;
            switch ($block) {
                case 3: printf ("\tGrade 3\n"); break;
                case 4: printf ("\n\tGrade 4\n"); break;
                case 5: printf ("\n\tGrade 5\n"); break;
                case 6: printf ("\n\tGrade 6a\n"); break;
                case 7: printf ("\n\tGrade 6b\n"); break;
                case 8: printf ("\n\tGrade 6c...\n"); break;
            }
        }

        if ($t == "Lead")
            $t = "L";
        else
            $t = "";
        */

        if (empty($n)) $n = "&nbsp;";
        if (empty($s)) $s = "&nbsp;";
        if (empty($e)) $e = "&nbsp;";
        if (empty($d)) { $d = "&nbsp;"; $a = "&nbsp;"; $m = "&nbsp;"; }

        printf ("<tr>");
        printf ("<td>%s</td>", $p);
        printf ("<td>%s</d>", $t);
        printf ("<td>%s</td>", $c);
        printf ("<td>%s</td>", $g);
        printf ("<td>%s</td>", $n);
        printf ("<td>%s</td>", $s);
        printf ("<td>%s</td>", $d);
        printf ("<td>%s</td>", $e);
        printf ("<td>%s</td>", $a);
        printf ("<td>%s</td>", $m);
        printf ("</tr>");

        //printf ("%s\t%s\t%s\t%s\n", $p, $c, $t, $g);

        $count++; if ($count > 5) break;
    }
    echo "</table>";

    //var_dump ($table_climb_type);

    /*
    foreach ($table_panel as $id => $panel) {
        $table_climb_type[$panel['type']]['count']++;
    }

    printf ("Panels:\n");
    foreach ($table_climb_type as $id => $type) {
        printf ("\t%s : %s\n", $type['type'], $type['count']);
    }
    */

    /*
    foreach ($table_route as $id => $route) {
        $table_grade[$route['grade']]['count']++;
    }

    printf ("Grades:\n");
    foreach ($table_grade as $id => $grade) {
        printf ("\t%s : %s\n", $grade['grade'], $grade['count']);
    }
    */

    /*
    $stat_wall_attrs = array();
    reset($table_panel);
    foreach (current ($table_panel) as $key => $value) {
        if (($key == "id") || ($key == "number") || ($key == "height") || ($key == "type"))
            continue;
        $stat_wall_attrs[$key] =  0;
    }

    foreach ($table_panel as $id => $panel) {
        foreach ($panel as $type => $present) {
            if (($type == "id") || ($type == "number") || ($type == "height") || ($type == "type"))
                continue;
            if (!$present)
                continue;
            $stat_wall_attrs[$type]++;
        }
    }

    printf ("Styles:\n");
    foreach ($stat_wall_attrs as $type => $count) {
        printf ("\t%s : %d\n", $type, $count);
    }
    */

    /*
    foreach ($table_route as $id => $route) {
        $table_setter[$route['setter']]['count']++;
    }

    printf ("Setter:<br>");
    foreach ($table_setter as $id => $setter) {
        if (empty ($setter['name']))
            continue;
        printf ("\t%s : %s<br>", $setter['name'], $setter['count']);
    }
    */

    mysql_close($db);
}

html_header();
main();

echo time();

?>

