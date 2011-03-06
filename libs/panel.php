<?php

set_include_path ('../../libs');

function panel_get()
{
	static $panel = null;

	if ($panel === null) {
		include 'db.php';
		include 'db_names.php';
		$panel = db_select($DB_PANEL);
	}

	return $panel;
}

function panel_match ($test)
{
	$panel = panel_get();
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

function panel_match_xml (&$xml)
{
	$panel = panel_match ($xml->input);
	if ($panel === null) {
		$xml->addChild ('error', "no such panel");
	} else {
		$xml->addChild ('panel', $panel['name']);
	}
}

