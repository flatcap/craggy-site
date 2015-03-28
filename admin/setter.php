<?php

set_include_path ('../libs');

include_once 'db.php';
include_once 'utils.php';

function setter_main()
{
	$output = '';

	$output .= html_header ('Setter', '../');
	$output .= '<body>';
	$output .= html_menu('../');

	$output .= "<div class='content'>";
	$output .= "<div class='title'>";
	$output .= '<h1>Setters</h1>';
	$output .= '</div>';

	$output .= "<div id='list_area'>";
	$output .= "<div id='setter_table'>";
	$output .= '</div>';

	$output .= '<br>';
	$output .= "<div class='buttons'>";
	$output .= "<input type='button' id='button_add' value='Add'>";
	$output .= '&nbsp;';
	$output .= "<input type='button' id='button_edit' value='Edit'>";
	$output .= '&nbsp;';
	$output .= "<input type='button' id='button_delete' value='Delete'>";
	$output .= '&nbsp;';
	$output .= "<input type='button' id='button_list' value='List'>";
	$output .= '</div>';
	$output .= '</div>';
	$output .= "<div id='notify_area'>";
	$output .= '</div>';
	$output .= "<div id='work_area'>";
	$output .= '</div>';
	$output .= '</div>';

	$output .= "<script type='text/javascript' src='complete.js'></script>";
	$output .= "<script type='text/javascript' src='dialog.js'></script>";
	$output .= "<script type='text/javascript' src='notify.js'></script>";
	$output .= "<script type='text/javascript' src='table.js'></script>";
	$output .= "<script type='text/javascript' src='xml.js'></script>";
	$output .= "<script type='text/javascript' src='setter.js'></script>";
	$output .= get_errors();
	$output .= html_footer();

	return $output;
}


date_default_timezone_set('Europe/London');

echo setter_main();
