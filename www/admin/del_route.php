<?php

set_include_path ('../../libs');

include 'db.php';
include 'utils.php';

function route_main()
{
	$output = '';
	$last_update = date ('j M Y', strtotime (db_get_last_update()));

	$output .= html_header ('Delete Routes', '../');
	$output .= '<body>';

	$output .= "<div class='header'>";
	$output .= "<img alt='craggy logo' src='../img/craggy2.png'>&nbsp;&nbsp;&nbsp;&nbsp;";
	$output .= "Delete Routes <span>(Last updated: $last_update)</span>";
	$output .= "</div>";
	$output .= html_menu('../');

	$output .= "<div class='content'>CONTENT";
	$output .= '<h2>Delete Routes</h2>';

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
