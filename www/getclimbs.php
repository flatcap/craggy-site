<?php

include "db.php";
include "utils.php";

$g_routes  = NULL;
$g_panels  = NULL;
$g_colours = NULL;


if (array_key_exists ('q', $_GET)) $q = trim ($_GET["q"]); else $q = "";
if (array_key_exists ('r', $_GET)) $r = trim ($_GET["r"]); else $r = "";

if (!empty ($r)) {
	echo "$r";
	return;
}

if (isset ($argc)) {
	if ($argc < 2) {
		printf ("args\n");
		return;
	}
	array_shift ($argv);
	$q = implode (" ", $argv);
} else {
	if (!isset ($_GET)) {
		echo "bad call: no get";
		return;
	}
		
	if (!array_key_exists ('q', $_GET)) {
		echo "bad call: no q";
		return;
	}

	$q=trim ($_GET["q"]);
	if (empty ($q)) {
		//echo "empty";
		return;
	}
}

$parts = preg_split("/[\s,]+/", $q);
$panel = array_shift ($parts);

if (!is_numeric ($panel)) {
	printf ("Not a panel number: '%s'", $panel);
	return;
}

$g_routes  = db_select("route");
$g_colours = db_select("colour");
$g_panels  = db_select("panel");

$panel_id = NULL;
foreach ($g_panels as $id => $p) {
	if ($p['number'] == $panel) {
		$panel_id = $p['id'];
		break;
	}
}

if ($panel_id === NULL) {
	printf ("Panel '%d' doesn't exist\n", $panel);
	return;
}

$matches = array();
foreach ($g_routes as $id => $r) {
	if ($r['panel'] == $panel_id) {
		$col_id = $r['colour'];
		$col = $g_colours[$r['colour']]['colour'];
		$matches[$col] = $r;
	}
}

$num_routes = count ($matches);
if ($num_routes == 0) {
	printf ("Panel '%d' doesn't have any routes", $panel);
	return;
}

$list = array();
foreach ($matches as $m) {
	$list[] = $g_colours[$m['colour']]['colour'];
}
printf ("%d %s", $panel, implode (", ", $list));

//echo $response;

