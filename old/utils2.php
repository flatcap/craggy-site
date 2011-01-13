<?php

function parse_single_colour ($string)
{
	$string = strtolower($string);

	switch ($string) {
		case 'beige':
		case 'beig':
		case 'bei':
		case 'be':
		case 'bg':
			$colour = 'Beige';
			break;
		case 'black':
		case 'blac':
		case 'bla':
		case 'blk':
			$colour = 'Black';
			break;
		case 'blue':
		case 'blu':
			$colour = 'Blue';
			break;
		case 'features':
		case 'feature':
		case 'featur':
		case 'featu':
		case 'feat':
		case 'fea':
		case 'fe':
		case 'ft':
			$colour = 'Features';
			break;
		case 'green':
		case 'gree':
		case 'gren':
		case 'grn':
		case 'gn':
			$colour = 'Green';
			break;
		case 'grey':
		case 'gray':
		case 'gry':
		case 'gy':
			$colour = 'Grey';
			break;
		case 'mushroom':
		case 'mushroo':
		case 'mushro':
		case 'mushr':
		case 'mush':
		case 'mus':
		case 'mu':
			$colour = 'Mushroom';
			break;
		case 'orange':
		case 'orang':
		case 'oran':
		case 'ora':
		case 'or':
			$colour = 'Orange';
			break;
		case 'pink':
		case 'pin':
		case 'pi':
		case 'pk':
			$colour = 'Pink';
			break;
		case 'purple':
		case 'purpl':
		case 'purp':
		case 'pur':
		case 'pu':
		case 'pp';
			$colour = 'Purple';
			break;
		case 'red':
		case 're':
		case 'rd':
			$colour = 'Red';
			break;
		case 'turquoise':
		case 'turquois':
		case 'turquoi':
		case 'turquo':
		case 'turqu':
		case 'turq':
		case 'tur':
		case 'tq':
		case 'tr':
			$colour = 'Turquoise';
			break;
		case 'white':
		case 'whit':
		case 'whi':
		case 'wh':
			$colour = 'White';
			break;
		case 'yellow':
		case 'yello':
		case 'yell':
		case 'yel':
		case 'ye':
		case 'yl':
			$colour = 'Yellow';
			break;
		default:
			$colour = '';
			break;
	}

	return $colour;
}

function parse_colour ($string)
{
	$string = strtolower($string);

	$pos = strpos ($string, '/');
	if ($pos === FALSE) {
		$colour = parse_single_colour($string);
	} else {
		$colour = parse_single_colour(substr($string, 0, $pos)) . '/' .
				  parse_single_colour(substr($string, $pos+1));
	}

	// then parse the colour against the database

	return $colour;
}

function parse_colour2($colour, $field)
{
	global $g_colours;
	$c = '';

	foreach ($g_colours as $id => $key) {
		if ($key['colour'] == $colour) {
			$c = $key['id'];
			break;
		}
	}

	return $c;
}

function parse_setter($setter, $field)
{
	global $g_setters;
	$result = NULL;

	foreach ($g_setters as $id => $key) {
		if (($key['initials'] == $setter) ||
			($key['name']     == $setter)) {
			$result = $key[$field];
			break;
		}
	}

	return $result;
}

function parse_grade($grade, $field)
{
	global $g_grades;
	$result = NULL;

	foreach ($g_grades as $id => $key) {
		if ($key['grade'] == $grade) {
			$result = $key[$field];
			break;
		}
	}

	return $result;
}

function parse_panel($panel, $field)
{
	global $g_panels;
	$result = NULL;

	foreach ($g_panels as $id => $key) {
		if ($key['number'] == $panel) {
			$result = $key[$field];
			break;
		}
	}

	return $result;
}

function grade_block($grade)
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

