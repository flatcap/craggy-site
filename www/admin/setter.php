<?php

set_include_path ("../../libs");

include "db.php";
include "utils.php";

function html_table_header2 ($columns)
{
	$output = "<thead><tr>";
	$output .= "<th><input type='checkbox' id='tick_master'></th>";

	foreach ($columns as $name) {
		$split = explode("_", $name);
		$n = array_pop($split);
		$n = ucfirst ($n);
		$output .= sprintf ("<th>%s</th>", $n);
	}

	$output .= "</tr></thead>";

	return $output;
}

function list_render_html2 (&$list, &$columns, &$widths, $ts_metadata = "")
{
	$output = "";

	/*
	if ($ts_metadata)
		$ts_metadata = " class='tablesorter {$ts_metadata}'";
	*/

	//$output .= "<table{$ts_metadata} border=1 cellspacing=0>";
	$output .= "<table border=1 cellspacing=0>";
	$output .= html_table_header2 ($columns);
	$output .= "<tbody>";

	// foreach row of list
	foreach ($list as $row) {

		$id = $row['id'];
		$output .= "<tr>";
		$output .= "<td><input type='checkbox' id='id_{$id}' value='{$id}'></td>";

		// foreach col of columns
		foreach ($columns as $col) {

			// consider justification of column
			if ($widths[$col] > 0) {
				$format = '<td class="right">%s</td>';
			} else {
				$format = '<td>%s</td>';
			}
			$output .= sprintf ($format, $row[$col]);
		}

		$output .= "</tr>";
	}

	$output .= "</tbody>";
	$output .= "</table>";

	return $output;
}

function db_select2 ($table, $columns = NULL, $where = NULL, $order = NULL, $group = NULL)
{
	if (isset($columns)) {
		if (is_array($columns))
			$cols = implode ($columns, ",");
		else
			$cols = $columns;

		$key = $columns[0];
		$key = "id";
	} else {
		$cols = "*";
		$key = "id";
	}

	$query = "select {$cols} from {$table}";

	if (isset ($where)) {
		if (is_array ($where))
			$w = implode ($where, " and ");
		else
			$w = $where;
		$query .= " where " . $w;
	}

	if (isset ($group)) {
		$query .= " group by " . $group;
	}

	if (isset ($order)) {
		$query .= " order by " . $order;
	}

	$db = db_get_database();

	//echo "$query;<br>";
	$result = mysql_query($query);

	$list = array();
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$list[$row[$key]] = $row;
	}

	mysql_free_result($result);
	return $list;
}


function setter_table()
{
	$output = "";

	$table   = "craggy_setter" .
			" left join craggy_route on (setter_id=craggy_setter.id)";

	$columns = array ("craggy_setter.id as id",
			"craggy_setter.name as setter",
			"count(craggy_route.id) as count");

	$where   = NULL;
	$order   = "id";
	$group   = "id";

	$list = db_select2 ($table, $columns, $where, $order, $group);
	//echo "<pre>"; var_dump ($list); echo "</pre>";

	$setters = array();
	foreach ($list as $s) {
		$name = $s['setter'];
		if (empty ($name)) {
			$name = "N/A";
		}
		if (array_key_exists ($name, $setters)) {
			$setters[$name]['count']++;
		} else {
			$setters[$name] = array ('id' => $s['id'], 'setter' => $name, 'count' => 1);
		}
	}

	$columns = array ('id', 'setter', 'count');
	$widths = column_widths ($setters, $columns, TRUE);
	fix_justification ($widths);

	$output .= list_render_html2 ($list, $columns, $widths, "{sortlist: [[2,1],[1,0]]}");

	return $output;
}

function setter_main()
{
	$output = "";
	$last_update = date ("j M Y", strtotime (db_get_last_update()));

	$output .= html_header ("Setter", "../");
	$output .= "<body>";

	$output .= "<div class='header'>Setters <span>(Last updated: $last_update)</span></div>\n";
	$output .= html_menu("../");

	$output .= "<div class='content'>\n";
	$output .= "<h2>Setters</h2>";

	$output .= "<div id='setter_table'>\n";
	$output .= "</div>\n";

	$output .= "<div class='buttons'>";
	$output .= "<br>";
	$output .= "<input type='submit' type='button' id='button_add' value='Add'>";
	$output .= "&nbsp;";
	$output .= "<input type='submit' type='button' id='button_edit' value='Edit'>";
	$output .= "&nbsp;";
	$output .= "<input type='submit' type='button' id='button_delete' value='Delete'>";
	$output .= "&nbsp;";
	$output .= "<input type='submit' type='button' id='button_list' value='List'>";
	$output .= "</div>";
	$output .= "</div>";

	$output .= "<script type='text/javascript' src='setter.js'></script>";
	$output .= get_errors();
	$output .= "</body>";
	$output .= "</html>";

	return $output;
}


date_default_timezone_set("UTC");

echo setter_main();
