<?php

set_include_path ('../libs');

include 'db.php';
include 'utils.php';

function index_main()
{
	$last_update = date ('j M Y', strtotime (db_get_last_update()));

	$output  = html_header ('Overview');
	$output .= '<body>';
	$output .= "<div id='header'>";
	$output .= "<img alt='craggy logo' src='img/craggy2.png'>&nbsp;&nbsp;&nbsp;&nbsp;";
	$output .= "Craggy Routes <span>(Last updated: $last_update)</span>";
	$output .= "</div>";
	$output .= html_menu();
	$output .= "<div id='content'>";
	$output .= get_stats();
	$output .= '</div>';
	$output .= get_errors();

	$output .= "<div id='footer'>";
	$output .= "Copyright &copy; 2006-2011 Rich Russon";
	$output .= '&nbsp;&mdash;&nbsp;';
	$output .= "Last Modified: 2011/01/05 16:45";
	$output .= '&nbsp;&mdash;&nbsp;';
	$output .= "Database 3 txn, 0.04s";
	$output .= '&nbsp;&mdash;&nbsp;';
	$output .= "Page Generated in 0.06s";
	$output .= "</div>";

	$output .= '</body>';
	$output .= '</html>';

	return $output;
}


date_default_timezone_set('UTC');
db_get_database();

echo index_main();

