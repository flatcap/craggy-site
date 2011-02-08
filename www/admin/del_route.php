<?php

set_include_path ('../../libs');

include 'db.php';
include 'utils.php';

function route_main()
{
	$output = '';

	$output .= html_header ('Delete Routes', '../');
	$output .= '<body>';
	$output .= html_menu('../');

	$output .= "<div class='content'>";
	$output .= "<div class='title'>";
	$output .= "<h1>Delete Routes</h1>";
	$output .= "</div>";

	$output .= "<div id='entry_area'>ENTRY";
	$output .= "<input id='entry' type='text' size='30' value='42-45'>";		// FOCUS
	$output .= "<input type='submit' type='button' id='button_list' value='List'>";
	$output .= "</div>";

	$output .= "<div id='notify_area'>NOTIFY</div>";
	$output .= "<div id='list_area'>LIST";
	$output .= "<div id='route_list'>ROUTE</div>";

	$output .= '<br>';
	$output .= "<div class='buttons'>";
	$output .= "<input type='submit' type='button' id='button_delete' value='Delete'>";
	$output .= '&nbsp;';
	$output .= "<input type='submit' type='button' id='button_cancel' value='Cancel'>";
	$output .= '</div>'; // buttons
	$output .= '</div>'; // list_area

	$output .= '</div>'; // content

	$output .= "<script type='text/javascript' src='del_route.js'></script>";

	$output .= get_errors();
	$output .= '</body>';
	$output .= '</html>';

	return $output;
}


date_default_timezone_set('UTC');

echo route_main();
