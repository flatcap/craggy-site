<?php

set_include_path ('../../libs');

include 'db.php';
include 'utils.php';

function add_main()
{
	$output = '';

	$output .= html_header ('Add Routes', '../');
	$output .= '<body>';
	$output .= html_menu('../');

	$output .= "<div class='content'>";
	$output .= "<div class='title'>";
	$output .= "<h1>Add Routes</h1>";
	$output .= "</div>";

	$output .= "<div id='entry_area'>";

	$output .= "<label for='date' accesskey='d'><u>D</u>ate</label>";
	$output .= "<input id='date' type='text' size='30' value='today'><br>";

	$output .= "<label for='setter' accesskey='s'><u>S</u>etter</label>";
	$output .= "<input id='setter' type='text' size='30' value='mc'><br>";

	$output .= "<label for='entry' accesskey='r'><u>R</u>outes</label>";
	//$output .= "<input id='entry' type='text' size='30' value='36 blue 3, red 3+, red 4, red 4+, red 5'>";
	$output .= "<input id='entry' type='text' size='30' value=''>";

	$output .= "<input type='submit' type='button' id='button_add' value='Add'>";
	$output .= "</div>";

	$output .= "<div id='notify_area'></div>";
	$output .= "<div id='list_area'>";
	$output .= "<div id='route_list'></div>";

	$output .= '<br>';
	$output .= "<div class='buttons'>";
	$output .= "<input type='submit' type='button' id='button_save' value='Save All'>";
	$output .= '&nbsp;';
	$output .= "<input type='submit' type='button' id='button_delete' value='Delete'>";
	$output .= '</div>'; // buttons
	$output .= '</div>'; // list_area

	$output .= '</div>'; // content

	$output .= "<script type='text/javascript' src='notify.js'></script>";
	$output .= "<script type='text/javascript' src='add_route.js'></script>";

	$output .= "<div id='debug_area'></div>";
	$output .= get_errors();
	$output .= '</body>';
	$output .= '</html>';

	return $output;
}


date_default_timezone_set('UTC');

echo add_main();
