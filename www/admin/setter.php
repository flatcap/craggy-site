<?php

set_include_path ("../../libs");

include "db.php";
include "utils.php";

function setter_main()
{
	$output = "";
	$last_update = date ("j M Y", strtotime (db_get_last_update()));

	$output .= html_header ("Setter", "../");
	$output .= "<body>";

	$output .= "<div class='header'>Setters <span>(Last updated: $last_update)</span></div>\n";
	$output .= html_menu("../");

	$output .= "<div class='content'>\n";
	$output .= "<h2>Setters</h2>";

	$output .= "<div id='setter_table'>\n";
	$output .= "</div>\n";

	$output .= "<br>";
	$output .= "<div class='buttons'>";
	$output .= "<input type='submit' type='button' id='button_add' value='Add'>";
	$output .= "&nbsp;";
	$output .= "<input type='submit' type='button' id='button_edit' value='Edit'>";
	$output .= "&nbsp;";
	$output .= "<input type='submit' type='button' id='button_delete' value='Delete'>";
	$output .= "&nbsp;";
	$output .= "<input type='submit' type='button' id='button_list' value='List'>";
	$output .= "</div>";
	$output .= "</div>";

	$output .= "<script type='text/javascript' src='setter.js'></script>";
	$output .= get_errors();
	$output .= "</body>";
	$output .= "</html>";

	return $output;
}


date_default_timezone_set("UTC");

echo setter_main();
