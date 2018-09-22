<?php

set_include_path ('libs');

include_once 'db.php';
include_once 'utils.php';

function index_main()
{
	$db = db_get_database();

	$last_update = date ('j M Y', strtotime (db_get_last_update($db)));

	$output  = html_header ('Overview');
	$output .= '<body>';
	$output .= '<div style="background: red; color: white; text-align: center;">This is historic climb data</div>';

	$output .= html_menu();

	$output .= "<div class='content'>";

	$output .= "<div class='title'>";
	$output .= "<h1>Craggy Routes</h1>";
	$output .= '</div>';	// title

	$output .= get_stats($db);
	$output .= '</div>';	// content
	$output .= get_errors();

	/*
	$output .= "<div class='footer'>";
	$output .= "Copyright &copy; 2006-2011 Rich Russon";
	$output .= '&nbsp;&mdash;&nbsp;';
	$output .= "Last Modified: 2011/01/05 16:45";
	$output .= '&nbsp;&mdash;&nbsp;';
	$output .= "Database 3 txn, 0.04s";
	$output .= '&nbsp;&mdash;&nbsp;';
	$output .= "Page Generated in 0.06s";
	$output .= "</div>";
	*/

	$output .= html_footer();

	$db->close();
	return $output;
}


date_default_timezone_set('Europe/London');

echo index_main();

