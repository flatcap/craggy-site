<?php

include "../db.php";
include "utils.php";

function add_main()
{
    $output = "";

    $type = get_url_variable('type');

    if ($type != "colour") {
        return "";
    }

    $id = 0;
    $output .= "<form name='focus' action='{$_SERVER['PHP_SELF']}' method='post'>\n";
    //$output .= "<input type='hidden' name='stage' value='2'>\n";
    $output .= "<input type='hidden' name='id_{$id}' value='{$r['id']}'>\n";
    $output .= "<td><input type='text' size='10' name='colour_{$id}' value=''></td>\n";
    $output .= "<td><input type='text' size='20' name='abbr_{$id}' value=''></td>\n";
    $output .= "</tr>\n";
    $output .= "<br>";
    //$output .= "<input name='button' accesskey='v' value='Verify' type='submit'>";
    $output .= "<input name='button' accesskey='o' value='OK' type='submit'>";
    $output .= "</form>";

    return $output;
}


date_default_timezone_set("UTC");
db_get_database();

//$g_colours = db_select("colour");
//$g_grades  = db_select("grade");
//$g_panels  = db_select("panel");
//$g_setters = db_select("setter");

$output = html_header ("Craggy Routes");
$output .= "<body>\n";
$output .= "<div class='header'>Craggy Routes</div>\n";
$output .= html_menu();
$output .= "<div class='content'>\n";

$output .= add_main();

$output .= "</div>\n";
$output .= get_errors();
$output .= "</body>\n";
$output .= "</html>";

echo $output;
