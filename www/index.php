<?php

set_include_path ('../libs');

include 'db.php';
include 'utils.php';

function index_main()
{
	$last_update = date ('j M Y', strtotime (db_get_last_update()));

	$output  = html_header ('Overview');
	$output .= '<body>';
	$output .= "<div class='header'>";
	$output .= "<img alt='craggy logo' src='img/craggy2.png'>&nbsp;&nbsp;&nbsp;&nbsp;";
	$output .= "Craggy Routes <span>(Last updated: $last_update)</span>";
	$output .= "</div>";
	$output .= html_menu();
	$output .= "<div class='content'>";
	$output .= get_stats();
	$output .= '</div>';
	$output .= get_errors();
	$output .= '</body>';
	$output .= '</html>';

	return $output;
}


date_default_timezone_set('UTC');
db_get_database();

echo index_main();

