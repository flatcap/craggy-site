<?php

set_include_path ('../../libs');

include 'db.php';
include 'utils.php';

function add_main()
{
	$output = '';
	$last_update = date ('j M Y', strtotime (db_get_last_update()));

	$output .= html_header ('Add Routes', '../');
	$output .= '<body>';

	$output .= "<div class='header'>";
	$output .= "<img alt='craggy logo' src='../img/craggy2.png'>&nbsp;&nbsp;&nbsp;&nbsp;";
	$output .= "Add Routes <span>(Last updated: $last_update)</span>";
	$output .= "</div>";
	$output .= html_menu('../');

	$output .= "<div class='content'>CONTENT";
	$output .= '<h2>Add Routes</h2>';

	$output .= "<div id='entry_area'>ENTRY<br>";

	$output .= "<label for='date' accesskey='d'><u>D</u>ate</label>";
	$output .= "<input id='date' type='text' size='30' value='today'><br>";

	$output .= "<label for='entry' accesskey='r'><u>R</u>outes</label>";
	$output .= "<input id='entry' type='text' size='30' value='45 red 5+, gn 6a, bg 6b'>";		// FOCUS

	$output .= "<input type='submit' type='button' id='button_add' value='Add'>";
	$output .= "</div>";

	$output .= "<div id='notify_area'>NOTIFY<br>more stuff</div>";
	$output .= "<div id='list_area'>LIST";
	$output .= "<div id='route_list'>ROUTE</div>";

	$output .= '<br>';
	$output .= "<div class='buttons'>";
	$output .= "<input type='submit' type='button' id='button_save' value='Save All'>";
	$output .= '&nbsp;';
	$output .= "<input type='submit' type='button' id='button_delete' value='Delete'>";
	$output .= '</div>'; // buttons
	$output .= '</div>'; // list_area

	$output .= '</div>'; // content

	//$output .= "<script type='text/javascript' src='add.js'></script>";

	$output .= get_errors();
	$output .= '</body>';
	$output .= '</html>';

	return $output;
}


date_default_timezone_set('UTC');

echo add_main();
