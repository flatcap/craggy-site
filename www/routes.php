<?php

set_include_path ('../libs');

include_once 'db.php';
include_once 'utils.php';

function routes_main($options)
{
	include 'db_names.php';

	$table   = $DB_V_ROUTE;
	$columns = array ('id', 'panel', 'colour', 'grade', 'climb_type', 'notes', 'setter', 'date_set');
	$order   = 'panel_seq, grade_seq, colour';
	//$order  .= ' limit 10';

	$list = db_select($table, $columns, null, $order);

	process_date ($list, 'date_set', true);
	process_key ($list);

	array_shift ($columns);		// Lose the id column
	$columns[] = 'age';
	$columns[] = 'months';

	// calculate widths (include headers?)
	$widths = column_widths ($list, $columns, true);

	// alter justification of widths
	fix_justification ($widths);

	$output = '';
	switch ($options['format']) {
		case 'html':
			$last_update = date ('j M Y', strtotime (db_get_last_update()));

			$output .= html_header ('Routes');
			$output .= '<body>';

			$output .= html_menu();

			$output .= "<div class='content'>\n";
			$output .= "<div class='title'>";
			$output .= "<h1>Route List</h1> <span>(Last updated: $last_update)</span>";
			$output .= "<span class='download'>";
			$output .= '<h3>Route Data</h3>';
			$output .= "<a href='?format=text'><img alt='route data as a formatted text document' width='32' height='32' src='img/txt.png'></a>";
			$output .= "<a href='?format=csv'><img alt='route data as a csv document' width='32' height='32' src='img/ss.png'></a>";
			$output .= "<a href='files/guildford.pdf'><img alt='route data as a pdf document' width='32' height='32' src='img/pdf.png'></a>";
			$output .= '</span>';
			$output .= '</div>';

			//$output .= "All Routes <span>(Last updated: $last_update)</span>";

			$output .= list_render_html ($list, $columns, $widths, '{sortlist: [[0,0], [2,0], [1,0]]}');
			$output .= '</div>';

			// use js
			//	to add error icon
			//	put anchor in get_errors
			//	link icon to anchor
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

			$output .= '</body>';
			$output .= '</html>';
			break;

		case 'csv':
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="routes.csv"');
			$output .= list_render_csv ($list, $columns);
			break;

		case 'text':
		default:
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="routes.txt"');
			$output .= list_render_text ($list, $columns, $widths);
			break;
	}

	return $output;
}


date_default_timezone_set('UTC');

$format = array ('csv', 'html', 'text');

if (isset ($argc)) {
	$longopts = array('format:');

	$options = getopt(null, $longopts);

	if (!array_key_exists ('format', $options) || !in_array ($options['format'], $format)) {
		$options['format'] = $format[2];
	}
} else {
	$options = array();

	$f = get_url_variable ('format');
	if (!in_array ($f, $format))
		$f = $format[1];

	$options['format'] = $f;
}

echo routes_main ($options);

