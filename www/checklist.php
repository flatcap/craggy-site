<?php

set_include_path ('../libs');

include 'db.php';
include 'utils.php';

function checklist_grade_block($grade)
{
	if ($grade[0] < '6')
		return $grade[0];

	$g = substr($grade, 0, 2);
	switch ($g) {
		case '6a': return 6;
		case '6b': return 7;
		default:   return 8;
	}
}

function list_render_tabs (&$list, &$columns)
{
	if (count ($list) == 0)
		return '';

	$output = '';

	// foreach row of list
	foreach ($list as $row) {

	  $out_row = array();
	  // foreach col of columns
	  foreach ($columns as $col) {
		array_push ($out_row, $row[$col]);
	  }

	  $output .= implode ($out_row, "\t") . "\r\n";
	}

	return $output;
}

function checklist_main ($options)
{
	include 'db_names.php';

	$table   = $DB_V_ROUTE;
	$columns = array ('id', 'panel', 'climb_type', 'colour', 'grade', 'grade_seq', 'notes', 'date_set');

	$list = db_select($table, $columns);

	usort($list, 'cmp_panel');

	process_key ($list);

	$checklist = array (3 => array(), 4 => array(), 5 => array(), 6 => array(), 7 => array());
	while ($row = array_shift ($list)) {

		$gb = checklist_grade_block ($row['grade']);
		$checklist[$gb][] = $row;
	}

	$output = '';
	//switch ($options['format']) {
	switch ($options['f']) {
		case 'html':
			$last_update = date ('j M Y', strtotime (db_get_last_update()));

			$output .= html_header ('Checklist');
			$output .= '<body>';

			$output .= "<div class='download'>";
			$output .= '<h1>Route Data</h1>';
			$output .= "<a href='?format=text'><img alt='checklist as a formatted text document' width='24' height='24' src='img/txt.png'></a>";
			$output .= '&nbsp;&nbsp;';
			$output .= "<a href='?format=csv'><img alt='checklist as a csv document' width='24' height='24' src='img/ss.png'></a>";
			$output .= '</div>';

			$output .= "<div class='header'>";
			$output .= "<img alt='craggy logo' src='img/craggy2.png'>&nbsp;&nbsp;&nbsp;&nbsp;";
			$output .= "Checklist (Routes in grade order) <span>(Last updated: $last_update)</span>";
			$output .= "</div>";
			$output .= html_menu();
			$output .= "<div class='content'>";
			break;

		case 'csv':
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="checklist.csv"');
			break;

		case 'tabs':
		case 'text':
		default:
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="checklist.txt"');
			break;
	}

	$titles = array (3 => 'Grade 3', 4 => 'Grade 4', 5 => 'Grade 5', 6 => 'Grade 6a', 7 => 'Grade 6b', 8 => 'Grade 6c...');
	$columns = array('panel', 'colour', 'grade', 'key');
	foreach ($checklist as $gb => $list) {

		$title = $titles[$gb];
		$count = count ($list);

		$widths = column_widths ($list, $columns, true);
		fix_justification ($widths);

		// render section
		//switch ($options['format']) {
		switch ($options['f']) {
			case 'html':
				$output .= "<h2>$title <span>($count)</span></h2>\n";
				$output .= list_render_html ($list, $columns, $widths, '{sortlist: [[0,0], [2,0], [1,0]]}');
				$output .= '<br>';
				break;

			case 'csv':
				$output .= list_render_csv ($list, $columns);
				$output .= '""' . "\r\n";
				break;

			case 'tabs':
				$output .= "$title ($count)\r\n";
				$output .= list_render_tabs ($list, $columns);
				$output .= "\r\n";
				break;

			case 'text':
			default:
				$output .= "$title ($count)\r\n";
				$output .= list_render_text ($list, $columns, $widths);
				$output .= "\r\n";
				break;
		}
	}

	//switch ($options['format']) {
	switch ($options['f']) {
		case 'html':
			$output .= '</div>';
			$output .= get_errors();
			$output .= '</body>';
			$output .= '</html>';
			break;

		case 'csv':
			break;

		case 'tabs':
		case 'text':
		default:
			break;
	}

	return $output;
}


date_default_timezone_set('UTC');

$format = array ('csv', 'html', 'text', 'tabs');

if (isset ($argc)) {
//	$longopts  = array('format:');

//	$options = getopt(null, $longopts);
//	if (!array_key_exists ('format', $options) || !in_array ($options['format'], $format)) {
//		$options['format'] = $format[2];
//	}

	$options = getopt('f:');
	if (!array_key_exists ('f', $options) || !in_array ($options['f'], $format)) {
		$options['f'] = $format[2];
	}
} else {
	$options = array();

	$f = get_url_variable ('format');
	if (!in_array ($f, $format))
		$f = $format[1];

	//$options['format'] = $f;
	$options['f'] = $f;
}

echo checklist_main ($options);

