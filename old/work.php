function cmp_colour($a, $b)
{
	$c1 = $a['colour'];
	$p1 = $a['panel'];
	$g1 = $a['grade_seq'];

	$c2 = $b['colour'];
	$p2 = $b['panel'];
	$g2 = $b['grade_seq'];

	if ($c1 != $c2)
		return ($c1 < $c2) ? -1 : 1;

	if ($p1 != $p2)
		return ($p1 < $p2) ? -1 : 1;

	return ($g1 < $g2) ? -1 : 1;
}

function cmp_priority($a, $b)
{
	$x1 = $a['priority'];
	$p1 = $a['panel'];
	$g1 = $a['grade_seq'];
	$c1 = $a['colour'];

	$x2 = $b['priority'];
	$p2 = $b['panel'];
	$g2 = $b['grade_seq'];
	$c2 = $b['colour'];

	if ($x1 != $x2)
		return ($x1 < $x2) ? -1 : 1;

	if ($p1 != $p2)
		return ($p1 < $p2) ? -1 : 1;

	if ($g1 != $g2)
		return ($g1 < $g2) ? -1 : 1;

	return ($c1 < $c2) ? -1 : 1;
}

function cmp_score($a, $b)
{
	$s1 = $a['score'];
	$p1 = $a['panel'];
	$g1 = $a['grade_seq'];
	$c1 = $a['colour'];

	$s2 = $b['score'];
	$p2 = $b['panel'];
	$g2 = $b['grade_seq'];
	$c2 = $b['colour'];

	if ($s1 != $s2)
		return ($s1 > $s2) ? -1 : 1;

	if ($p1 != $p2)
		return ($p1 < $p2) ? -1 : 1;

	if ($g1 != $g2)
		return ($g1 < $g2) ? -1 : 1;

	return ($c1 < $c2) ? -1 : 1;
}

function cmp_type($a, $b)
{
	$t1 = $a['climb_type'];
	$p1 = $a['panel'];
	$g1 = $a['grade_seq'];
	$c1 = $a['colour'];

	$t2 = $b['climb_type'];
	$p2 = $b['panel'];
	$g2 = $b['grade_seq'];
	$c2 = $b['colour'];

	if ($t1 != $t2)
		return ($t1 < $t2) ? -1 : 1;

	if ($p1 != $p2)
		return ($p1 < $p2) ? -1 : 1;

	if ($g1 != $g2)
		return ($g1 < $g2) ? -1 : 1;

	return ($c1 < $c2) ? -1 : 1;
}


function work_priority(&$list, $pri)
{
	foreach ($list as $index => $row) {
		$list[$index]['priority'] = $pri;
	}
}

function work_todo()
{
	include 'db_names.php';

	$climber_id = 1;

	$table   = $DB_ROUTE .
			" left join $DB_CLIMB      on (($DB_CLIMB.route_id      = $DB_ROUTE.id) and (climber_id = {$climber_id}))" .
			" left join $DB_COLOUR     on ($DB_ROUTE.colour_id      = $DB_COLOUR.id)" .
			" left join $DB_PANEL      on ($DB_ROUTE.panel_id       = $DB_PANEL.id)" .
			" left join $DB_GRADE      on ($DB_ROUTE.grade_id       = $DB_GRADE.id)" .
			" left join $DB_CLIMB_TYPE on ($DB_PANEL.climb_type_id  = $DB_CLIMB_TYPE.id)" .
			" left join $DB_SUCCESS    on ($DB_CLIMB.success_id     = $DB_SUCCESS.id)" .
			" left join $DB_RATING     on ($DB_RATING.route_id      = $DB_ROUTE.id)" .
			" left join $DB_DIFFICULTY on ($DB_RATING.difficulty_id = $DB_DIFFICULTY.id)" .
			" left join $DB_CLIMB_NOTE on ($DB_RATING.climb_note_id = $DB_CLIMB_NOTE.id)";

	$columns = array (
			  "$DB_ROUTE.id               as route_id",
			  "$DB_PANEL.name             as panel",
			  "$DB_COLOUR.colour          as colour",
			  "$DB_GRADE.grade            as grade",
			  "$DB_GRADE.sequence         as grade_seq",
			  "climb_type",
			  "date_climbed",
			  "success_id",
			  "$DB_SUCCESS.outcome        as success",
			  "nice                       as n",
			  "$DB_DIFFICULTY.description as diff",
			  "notes");

	$where   = array ('date_end is null', '((success_id < 3) OR (success_id is null))', "$DB_GRADE.sequence < 600");

	$list = db_select2($table, $columns, $where);

	work_priority ($list, 'T');
	return $list;
}

function work_downclimb()
{
	include 'db_names.php';

	$climber_id = 1;

	$table   = $DB_ROUTE .
			" left join $DB_CLIMB      on (($DB_CLIMB.route_id      = $DB_ROUTE.id) and (climber_id = {$climber_id}))" .
			" left join $DB_COLOUR     on ($DB_ROUTE.colour_id      = $DB_COLOUR.id)" .
			" left join $DB_PANEL      on ($DB_ROUTE.panel_id       = $DB_PANEL.id)" .
			" left join $DB_GRADE      on ($DB_ROUTE.grade_id       = $DB_GRADE.id)" .
			" left join $DB_CLIMB_TYPE on ($DB_PANEL.climb_type_id  = $DB_CLIMB_TYPE.id)" .
			" left join $DB_SUCCESS    on ($DB_CLIMB.success_id     = $DB_SUCCESS.id)" .
			" left join $DB_RATING     on ($DB_RATING.route_id      = $DB_ROUTE.id)" .
			" left join $DB_DIFFICULTY on ($DB_RATING.difficulty_id = $DB_DIFFICULTY.id)" .
			" left join $DB_CLIMB_NOTE on ($DB_RATING.climb_note_id = $DB_CLIMB_NOTE.id)";

	$columns = array ("$DB_ROUTE.id               as route_id",
			  "$DB_PANEL.name             as panel",
			  "$DB_COLOUR.colour          as colour",
			  "$DB_GRADE.grade            as grade",
			  "$DB_GRADE.sequence         as grade_seq",
			  "climb_type",
			  "date_climbed",
			  "success_id",
			  "$DB_SUCCESS.outcome        as success",
			  "nice                       as n",
			  "$DB_DIFFICULTY.description as diff",
			  "notes");

	$where   = array ('date_end is null', 'success_id <> 4', "$DB_GRADE.sequence < 400");

	$list = db_select2($table, $columns, $where);

	work_priority ($list, 'D');
	return $list;
}

function work_seldom_range ($m_start, $m_finish)
{
	include 'db_names.php';

	$when_start  = db_date ("$m_start months ago");

	$climber_id = 1;

	$table   = $DB_ROUTE .
			" left join $DB_CLIMB      on (($DB_CLIMB.route_id      = $DB_ROUTE.id) and (climber_id = {$climber_id}))" .
			" left join $DB_COLOUR     on ($DB_ROUTE.colour_id      = $DB_COLOUR.id)" .
			" left join $DB_PANEL      on ($DB_ROUTE.panel_id       = $DB_PANEL.id)" .
			" left join $DB_GRADE      on ($DB_ROUTE.grade_id       = $DB_GRADE.id)" .
			" left join $DB_CLIMB_TYPE on ($DB_PANEL.climb_type_id  = $DB_CLIMB_TYPE.id)" .
			" left join $DB_SUCCESS    on ($DB_CLIMB.success_id     = $DB_SUCCESS.id)" .
			" left join $DB_RATING     on ($DB_RATING.route_id      = $DB_ROUTE.id)" .
			" left join $DB_DIFFICULTY on ($DB_RATING.difficulty_id = $DB_DIFFICULTY.id)" .
			" left join $DB_CLIMB_NOTE on ($DB_RATING.climb_note_id = $DB_CLIMB_NOTE.id)";

	$columns = array (
			  "$DB_ROUTE.id               as route_id",
			  "$DB_PANEL.name             as panel",
			  "$DB_COLOUR.colour          as colour",
			  "$DB_GRADE.grade            as grade",
			  "$DB_GRADE.sequence         as grade_seq",
			  "climb_type",
			  "date_climbed",
			  "success_id",
			  "$DB_SUCCESS.outcome        as success",
			  "nice                       as n",
			  "$DB_DIFFICULTY.description as diff",
			  "notes");

	$where   = array ('date_end is null', "$DB_GRADE.sequence < 600", "date_climbed < '$when_start'");

	if (isset ($m_finish)) {
		$when_finish = db_date ("$m_finish months ago");
		array_push ($where, "date_climbed > '$when_finish'");
	}

	$list = db_select2($table, $columns, $where);

	return $list;
}

function work_seldom()
{
	$output = array();
	$ranges = array (6, 4, 3, 2);

	$start  = null;
	$finish = null;
	foreach ($ranges as $num) {
		$finish = $start;
		$start  = $num;

		$list = work_seldom_range ($start, $finish);
		work_priority ($list, $start);

		$output = array_merge ($output, $list);
	}

	return $output;
}

function work_flatten ($list)
{
	$output = array();

	$old = null;
	foreach ($list as $row) {
		$new = $row['panel'] . $row['colour'] . $row['grade'];
		if ($new != $old) {
			$output[] = $row;
			$old = $new;
		}
	}

	return $output;
}

