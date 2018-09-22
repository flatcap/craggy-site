<?php

set_include_path ('libs');

include_once 'db.php';
include_once 'utils.php';

function six_main ($options)
{
	include 'db_names.php';

	$db = db_get_database();

	$table   = $DB_ROUTE .
			" left join $DB_COLOUR     on ($DB_ROUTE.colour_id      = $DB_COLOUR.id)" .
			" left join $DB_PANEL      on ($DB_ROUTE.panel_id       = $DB_PANEL.id)" .
			" left join $DB_GRADE      on ($DB_ROUTE.grade_id       = $DB_GRADE.id)" .
			" left join $DB_CLIMB_TYPE on ($DB_PANEL.climb_type_id  = $DB_CLIMB_TYPE.id)" .
			" left join $DB_RATING     on ($DB_RATING.route_id      = $DB_ROUTE.id)" .
			" left join $DB_DIFFICULTY on ($DB_RATING.difficulty_id = $DB_DIFFICULTY.id)";

	$columns = array (
			  "$DB_ROUTE.id               as route_id",
			  "$DB_PANEL.name             as panel",
			  "$DB_COLOUR.colour          as colour",
			  "$DB_GRADE.grade            as grade",
			  "$DB_PANEL.height           as height",
			  "climb_type",
			  "nice                       as n",
			  "$DB_DIFFICULTY.description as diff");

	# where climber_id = 1 or null
	$where   = array ("date_end is null", "$DB_GRADE.sequence >= 400", "$DB_GRADE.sequence < 500", "$DB_CLIMB_TYPE.id <> 1");
	$order   = "$DB_PANEL.sequence, $DB_GRADE.sequence, colour";

	$list = db_select2($db, $table, $columns, $where, $order);

	$columns = array ('id', 'panel', 'colour', 'grade', 'diff', 'height');

	$total_height = process_height_total ($list);
	process_height_abbreviate ($list);

	array_shift ($columns);		// Ignore the id column
	$widths = column_widths ($list, $columns, true);
	fix_justification ($widths);

	$count  = count ($list);
	$output = '';
	switch ($options['format']) {
		case 'html':
			$last_update = date ('j M Y', strtotime (db_get_last_update($db)));

			$output .= html_header ('6a');
			$output .= '<body>';
			$output .= '<div style="background: red; color: white; text-align: center;">This is historic climb data</div>';

			$output .= html_menu();

			$output .= "<div class='content'>\n";

			$output .= "<div class='title'>";
			$output .= "<h1>Top Roped 6a Routes</h1>";
			$output .= "<span class='download'>";
			$output .= '<h3>Route Data</h3>';
			//$output .= "<a href='guildford_6a.pdf'><img alt='6a route list as a pdf document' width='32' height='32' src='style/pdf.png'></a>";
			$output .= "<a href='?format=text'><img alt='6a route list as a formatted text document' width='32' height='32' src='style/txt.png'></a>";
			$output .= "<a href='?format=csv'><img alt='6a route list as a csv document' width='32' height='32' src='style/ss.png'></a>";
			$output .= '</span>';

			$output .= '</div>';	// title

			$output .= "<p>$count climbs ({$total_height}m)</p>";
			$output .= list_render_html ($list, $columns, $widths, '{sortlist: [[0,0],[2,0],[1,0]]}');
			$output .= '</div>';	// content
			$output .= get_errors();
			$output .= html_footer();
			break;

		case 'csv':
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="guildford_6a.csv"');
			$output .= list_render_csv ($list, $columns);
			break;

		case 'text':
		default:
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="guildford_6a.txt"');
			$output .= list_render_text ($list, $columns, $widths);
			$output .= "\r\n$count climbs ({$total_height}m)\r\n";
			break;
	}

	return $output;
}


date_default_timezone_set('Europe/London');

$format = array ('csv', 'html', 'text');

if (isset ($argc)) {
	$longopts  = array('format:');

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

echo six_main ($options);

