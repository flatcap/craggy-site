<?php

set_include_path ('../libs');

include_once 'utils.php';

function panel_main()
{
	$output = '';

	$output .= html_header ('Edit Panels', '../');
	$output .= '<body>';
	$output .= html_menu('../');

	$output .= "<div class='content'>";
	$output .= "<div class='title'>";
	$output .= "<h1>Edit Panels</h1>";
	$output .= "</div>";

	$output .= "<div id='entry_area'>";

	$output .= "<label for='sequence' accesskey='s'><u>S</u>equence</label>";
	$output .= "<input type='checkbox' id='sequence'><br>";
	$output .= "<label for='entry' accesskey='p'><u>P</u>anels</label>";
	$output .= "<input id='entry' type='text' size='30' value=''>";
	$output .= "<input type='button' id='button_list' value='List'>";
	$output .= "</div>";

	$output .= "<div id='notify_area'></div>";
	$output .= "<div id='list_area'>";
	$output .= "<div id='panel_list'></div>";

	$output .= '<br>';
	$output .= "<div class='buttons'>";
	$output .= "<input type='button' id='button_save' value='Save All'>";
	$output .= '&nbsp;';
	$output .= "<input type='button' id='button_reset' value='Reset'>";
	$output .= '&nbsp;';
	$output .= "<input type='button' id='button_cancel' value='Cancel'>";
	$output .= '</div>'; // buttons
	$output .= '</div>'; // list_area

	$output .= '</div>'; // content

	$output .= "<script type='text/javascript' src='complete.js'></script>";
	$output .= "<script type='text/javascript' src='dialog.js'></script>";
	$output .= "<script type='text/javascript' src='notify.js'></script>";
	$output .= "<script type='text/javascript' src='table.js'></script>";
	$output .= "<script type='text/javascript' src='xml.js'></script>";
	$output .= "<script type='text/javascript' src='edit_panel.js'></script>";

	$output .= get_errors();
	$output .= html_footer();

	return $output;
}


date_default_timezone_set('Europe/London');

echo panel_main();

