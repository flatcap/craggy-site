<?php

function mark_climb_type ($row, &$old_value)
{
	$v = $row['climb_type'];

	$result = ($v != $old_value);
	if ($result)
		$old_value = $v;

	return $result;
}

function mark_colour ($row, &$old_value)
{
	$v = $row['colour'];

	$result = ($v != $old_value);
	if ($result)
		$old_value = $v;

	return $result;
}

function mark_date_set ($row, &$old_value)
{
	$v = strtotime ($row['date_set']);

	// Did the dates occur in the same week?
	$ls1 = strtotime ("last sunday", $old_value);
	$ls2 = strtotime ("last sunday", $v);
	$result = ($ls1 != $ls2);
	if ($result)
		$old_value = $v;

	return $result;
}

function mark_date_climbed ($row, &$old_value)
{
	$v = strtotime ($row['date_climbed']);

	// Did the dates occur in the same week?
	$ls1 = strtotime ("last sunday", $old_value);
	$ls2 = strtotime ("last sunday", $v);
	$result = ($ls1 != $ls2);
	if ($result)
		$old_value = $v;

	return $result;
}

function mark_downclimb ($row, &$old_value)
{
	$v = $row['d'];

	$result = ($v != $old_value);
	if ($result)
		$old_value = $v;

	return $result;
}

function mark_grade ($row, &$old_value)
{
	$v = $row['grade'];

	$result = ($v != $old_value);
	if ($result)
		$old_value = $v;

	return $result;
}

function mark_nice ($row, &$old_value)
{
	$v = $row['n'];

	$result = ($v != $old_value);
	if ($result)
		$old_value = $v;

	return $result;
}

function mark_onsight ($row, &$old_value)
{
	$v = $row['o'];

	$result = ($v != $old_value);
	if ($result)
		$old_value = $v;

	return $result;
}

function mark_panel ($row, &$old_value)
{
	$v = $row['panel'];

	$result = ($v != $old_value);
	if ($result)
		$old_value = $v;

	return $result;
}

function mark_setter ($row, &$old_value)
{
	$v = $row['setter'];

	$result = ($v != $old_value);
	if ($result)
		$old_value = $v;

	return $result;
}

function mark_success ($row, &$old_value)
{
	$v = $row['success'];

	$result = ($v != $old_value);
	if ($result)
		$old_value = $v;

	return $result;
}

