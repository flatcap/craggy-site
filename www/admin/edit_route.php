<?php

set_include_path ('../../libs');

include 'utils.php';

function route_main()
{
	$output = '';

	$output .= html_header ('Edit Routes', '../');
	$output .= '<body>';
	$output .= html_menu('../');

	$output .= "<div class='content'>";
	$output .= "<div class='title'>";
	$output .= "<h1>Edit Routes</h1>";
	$output .= "</div>";


	$output .= "<div id='entry_area'>";
	$output .= "<label for='entry' accesskey='p'><u>P</u>anels</label>";
	$output .= "<input id='entry' type='text' size='30' value='64-68'>";
	$output .= "<input type='submit' type='button' id='button_list' value='List'>";
	$output .= "</div>";

	$output .= "<div id='notify_area'></div>";
	$output .= "<div id='list_area'>";
	$output .= "<div id='route_list'></div>";

	$output .= '<br>';
	$output .= "<div class='buttons'>";
	$output .= "<input type='submit' type='button' id='button_save' value='Save All'>";
	$output .= '&nbsp;';
	$output .= "<input type='submit' type='button' id='button_reset' value='Reset'>";
	$output .= '&nbsp;';
	$output .= "<input type='submit' type='button' id='button_cancel' value='Cancel'>";
	$output .= '</div>'; // buttons
	$output .= '</div>'; // list_area

	$output .= '</div>'; // content

	$output .= "<script type='text/javascript' src='notify.js'></script>";
	$output .= "<script type='text/javascript' src='input.js'></script>";
	$output .= "<script type='text/javascript' src='xml.js'></script>";
	$output .= "<script type='text/javascript' src='edit_route.js'></script>";

	$output .= get_errors();
	$output .= '</body>';
	$output .= '</html>';

	return $output;
}


date_default_timezone_set('UTC');

echo route_main();
