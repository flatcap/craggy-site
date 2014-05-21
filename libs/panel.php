<?php

set_include_path ('../libs');

function panel_get($db)
{
	static $panel = null;

	if ($panel === null) {
		include_once 'db.php';
		include 'db_names.php';
		$panel = db_select($db, $DB_PANEL);
	}

	return $panel;
}

function panel_match ($db, $test)
{
	$panel = panel_get($db);
	if (!$panel)
		return null;

	if (empty ($test))
		return null;

	foreach ($panel as $key => $g) {
		if (strcasecmp ($test, $g['name']) == 0) {
			return $panel[$key];
		}
	}

	return null;
}

function panel_match_xml ($db, &$xml, $test)
{
	$panel = panel_match ($db, $test);
	if ($panel === null) {
		$xml->addChild ('error', sprintf ("'%s' is not a valid panel", $test));
		return false;
	} else {
		$xml->panel    = $panel['name'];
		$xml->panel_id = $panel['id'];
		return true;
	}
}

