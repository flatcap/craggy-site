<?php

function name_uppercase ($name)
{
	// uppercase first letter (MacX, McX and O'X too)
	$name = trim (str_replace ('_', ' ', $name));
	$name = ucwords (strtolower ($name));

	$len = strlen ($name);
	$pos = strpos ($name, "'");
	if ($pos !== false) {
		$pos++;
		if ($pos < $len) {
			$name[$pos] = strtoupper ($name[$pos]);
		}
	}

	if (strncasecmp ($name, 'mac', 3) == 0) {
		if ($len > 3) {
			$name[3] = strtoupper ($name[3]);
		}
	}

	if (strncasecmp ($name, 'mc', 2) == 0) {
		if ($len > 2) {
			$name[2] = strtoupper ($name[2]);
		}
	}

	return $name;
}

/* name_parse
 * firstname surname
 * surname, firstname
 * first second [...] surname
 * surname, first second [...]
 * first "sur name"
 * first sur_name
 * sur name parts, first
 *
 * but not
 *	first 'sur name'
 * leave ' for O'Keefe, etc
 */
function name_parse ($name)
{
	$name = trim ($name);
	if (empty ($name))
		return null;

	// convert backtick to apostrophe
	$name = str_replace ('`', "'", $name);

	$len = strlen ($name);
	$quote = false;
	$comma = -1;
	for ($i = 0; $i < $len; $i++) {
		if ($name[$i] == '"') {
			$quote = !$quote;
			$name[$i] = ' ';
			continue;
		}
		if ($name[$i] == ',') {
			if ($comma == -1) {
				$comma = $i;
			} else {
				$name[$i] = ' ';
			}
		}
		if (!$quote)
			continue;
		if ($name[$i] == ' ') {
			$name[$i] = '_';
		}
	}

	if (strpos ($name, ',') !== false) {
		$words = preg_split ("/,/", $name, 0, PREG_SPLIT_NO_EMPTY);
		$surname = array_shift ($words);
	} else {
		$words = preg_split ("/(.+)[\s]/", trim ($name), 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		$surname = array_pop ($words);
	}

	$rest = implode (' ', $words);
	$result = preg_split ("/[\s]/", $rest, 0, PREG_SPLIT_NO_EMPTY);
	array_unshift ($result, $surname);

	foreach ($result as &$part) {
		$part = name_uppercase ($part);
	}

	return $result;
}


