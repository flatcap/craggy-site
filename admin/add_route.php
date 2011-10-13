<?php

set_include_path ('../libs');

include_once 'utils.php';

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
	$output .= "<input id='date' type='text' size='30' value='today'>";
	$output .= "<span id='date_error'> </span> <br>";

	$output .= "<label for='setter' accesskey='s'><u>S</u>etter</label>";
	$output .= "<input id='setter' type='text' size='30' value='mc'>";
	$output .= "<span id='setter_error'></span><br>";

	$output .= "<label for='entry' accesskey='r'><u>R</u>outes</label>";
	$output .= "<input id='entry' type='text' size='30' value=''>";

	$output .= "<input type='button' id='button_add' value='Add'>";
	$output .= "</div>";

	$output .= "<div id='notify_area'></div>";
	$output .= "<div id='list_area'>";
	$output .= "<div id='route_list'></div>";

	$output .= '<br>';
	$output .= "<div class='buttons'>";
	$output .= "<input type='button' id='button_save' value='Save All'>";
	$output .= '&nbsp;';
	$output .= "<input type='button' id='button_clear' value='Clear'>";
	$output .= '</div>'; // buttons
	$output .= '</div>'; // list_area

	$output .= '</div>'; // content

	$output .= "<script type='text/javascript' src='complete.js'></script>";
	$output .= "<script type='text/javascript' src='dialog.js'></script>";
	$output .= "<script type='text/javascript' src='notify.js'></script>";
	$output .= "<script type='text/javascript' src='table.js'></script>";
	$output .= "<script type='text/javascript' src='xml.js'></script>";
	$output .= "<script type='text/javascript' src='add_route.js'></script>";

	$output .= get_errors();
	$output .= '</body>';
	$output .= '</html>';

	return $output;
}


date_default_timezone_set('Europe/London');

echo add_main();

