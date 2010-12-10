<?php

set_include_path ("/home/craggy/www");

include "db.php";
include "utils.php";

function age_main()
{
	$table   = "v_route";
	$columns = array ("id", "panel", "colour", "grade", "grade_seq", "climb_type", "date_set", "date_climbed", "success", "d", "o");
	$where   = NULL;
	$order   = NULL; //"date_set"

	$list = db_select($table, $columns, $where, $order);

	$route_count = 0;
	$routes_by_grade = array();
	$coverage_tried = 0;
	$coverage_clean = 0;
	$coverage_down  = 0;
	$age_by_month = array();
	$total_age = 0;
	$total_onsight = 0;

	$route_count = count ($list);

	process_date ($list, "date_set", TRUE);

	foreach ($list as $id => $route) {
		$grade = $route['grade'];
		$num   = $route['grade_seq'];
		if (!array_key_exists ($num, $routes_by_grade))
			$routes_by_grade[$num] = array();

		$route_ptr = &$routes_by_grade[$num];
		$route_ptr['grade'] = $grade;

		$climb_type = $route['climb_type'];
		if (!array_key_exists ($climb_type, $route_ptr))
			$route_ptr[$climb_type] = 0;

		$route_ptr[$climb_type]++;

		if ($route['success'] == 'clean')
			$coverage_clean++;
		if (!empty ($route['success']))
			$coverage_tried++;
		if ($route['d'] == '1')
			$coverage_down++;

		if (empty ($route['date_set']))
			$month = -1;
		else
			$month = min ((int) ($route['months']), 7);

		if (!array_key_exists ($month, $age_by_month))
			$age_by_month[$month] = 0;

		$age_by_month[$month]++;

		$total_age += $route['age'];

		$total_onsight += $route['o'];
	}

	/*
	printf ("\n");
	printf ("Number of routes: %d\n", $route_count);
	printf ("\n");

	ksort ($routes_by_grade, SORT_NUMERIC);
	printf ("Routes by grade:\n");
	printf ("\tGrade T/R Lead Auto\n");
	foreach ($routes_by_grade as $grade => $counts) {
		array_key_exists ('Top Rope',   $counts) ? $tr   = $counts['Top Rope']   : $tr   = "";
		array_key_exists ('Lead',       $counts) ? $lead = $counts['Lead']       : $lead = "";
		array_key_exists ('Auto-belay', $counts) ? $auto = $counts['Auto-belay'] : $auto = "";
		printf ("\t %-3s %3s %3s   %s\n", $counts['grade'], $tr, $lead, $auto);
	}
	printf ("\n");

	printf ("Coverage:\n");
	printf ("\tTried: %d\n", $coverage_tried);
	printf ("\tClean: %d\n", $coverage_clean);
	printf ("\tDown:  %d\n", $coverage_down);
	printf ("\n");

	ksort ($age_by_month);
	printf ("Route age by month\n");
	printf ("\tAge Count\n");
	foreach ($age_by_month as $age => $count) {
		printf ("\t%3d %3d\n", $age, $count);
	}
	printf ("\n");

	printf ("Total age = %d days\n", $total_age);
	printf ("\n");

	printf ("Total onsight = %d\n", $total_onsight);
	printf ("\n");
	*/

	exit(1);

	$json = json_encode ($list);

	//return $json;
}


echo (age_main() . "\n");

/*
v_route
id: 1
panel: 1
colour: Yellow
grade: 5+
grade_seq: 350
climb_type: Lead
date_set: 2010-08-02
date_climbed: 2010-09-08
success: clean
d: 1
o: 1

route
id: 1
panel: 1
colour: 18
grade: 6
date_set: 2010-08-02
date_climbed: 2010-09-08
success: clean
downclimb: 1
onsight: 1
*/
