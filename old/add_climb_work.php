
function climb_commit_notes (&$xml, &$ratings)
{
	foreach ($ratings as $r) {
		printf ("%-5s %-40s %-20s\n", $r['climb_note_id'], $r['climb_note'], $r['notes']);
	}

	//var_dump ($ratings);
	exit (1);

	// Have route id, notes
	//
	/*
		Original	New
		Rating		Rating		To do
	   ---------------------------------------------
		   --		   --		nothing

		   --		NEW UNIQ	create note
						add id to rating

		   --		NEW SHARED	use existing id
						add id to rating

		OLD UNIQ	   --		delete note
						add null to rating

		OLD SHARED	   --		do nothing
						add null to rating

		OLD UNIQ	OLD UNIQ	do nothing

		OLD SHARED	OLD SHARED	do nothing

		OLD UNIQ	NEW UNIQ	overwrite note
						add id to rating

		OLD UNIQ	NEW SHARED	delete note
						add id to rating

		OLD SHARED	NEW UNIQ	create note
						add id to rating

		OLD SHARED	NEW SHARED	use existing id
						add id to rating

	*/

	$count = 0;
	foreach ($ratings as &$r) {
		$n = trim ($r['notes']);
		if (empty ($n)) {
			$r['climb_note_id'] = null;
			continue;
		}

		$query = "insert into climb_note set notes = '$n'";
		$result = mysql_query($query);
		if ($result === true) {
			$r['climb_note_id'] = mysql_insert_id();
			$count++;
		} else {
			climb_add_error ($xml, "add note failed");
			$r['climb_note_id'] = null;
		}
	}
	if ($count > 0) {
		climb_add_error ($xml, sprintf ("Created %d notes", $count));
		printf ("Created %d notes\n\n", $count);
	}

	return $count;
}

function climb_get_all_panel ($name)
{
	global $g_routes;

	$results = array();
	foreach ($g_routes as $p) {
		if ($p['panel'] == $name) {
			$results[] = $p;
		}
	}

	return $results;
}

function climb_get_route_lookup()
{
	global $g_routes;

	$routes = array();
	foreach ($g_routes as $p) {
		$key = $p['panel'] . '_' . $p['colour'];
		$routes[$key] = $p['id'];
	}

	return $routes;
}

function climb_valid_command (&$xml)
{
	global $_GET;

	$success = true;

	if (!isset ($_GET)) {
		climb_add_error ($xml, "No get");
		$success = false;
	} else {
		if (!array_key_exists ('action', $_GET)) {
			climb_add_error ($xml, "No action");
			$success = false;
		}

		if (!array_key_exists ('climbs', $_GET)) {
			climb_add_error ($xml, "No climbs");
			$success = false;
		} else {
			$climbs = trim ($_GET['climbs']);
			if (empty ($climbs)) {
				climb_add_error ($xml, "Empty climbs");
				$success = false;
			}
		}

		if (!array_key_exists ('climber', $_GET)) {
			climb_add_error ($xml, "No climber");
			$success = false;
		}
	}

	return $success;
}


function get_random_climbs()
{
	$nice1 = (rand (0,3) == 0) ? "nice" : "";
	$nice2 = (rand (0,6) == 0) ? "nice" : "";
	$nice3 = (rand (0,9) == 0) ? "nice" : "";

	$diffs = array ('very easy', 'easy', 'medium', 'hard', 'very hard');

	$diff1 = $diffs[rand(0,4)];
	$diff2 = $diffs[rand(0,4)];
	$diff3 = $diffs[rand(0,4)];

	$notes1 = sprintf ("notes %d blah", rand (0,9));
	$notes2 = sprintf ("notes %d blah", rand (0,9));
	$notes3 = sprintf ("notes %d blah", rand (0,9));

	if (rand (0,3) == 0) $notes1 = null;
	if (rand (0,5) == 0) $notes2 = null;
	if (rand (0,7) == 0) $notes3 = null;

	$date_base = strtotime ("2007-01-01");
	$date_ref  = strtotime ("2011-02-22");
	$date_now  = strtotime ("now");

	$date = $date_base + (($date_now - $date_ref) * 1440);	// 1 day per minute after ref
	$datestr = strftime('%Y-%m-%d', $date);

	$successes = array ("failed", "success", "clean", "downclimb");

	$succ1 = $successes[rand (0,3)];
	$succ2 = $successes[rand (0,3)];
	$succ3 = $successes[rand (0,3)];

	$xml = "<list type='climb'>";

	$xml .= sprintf ("<climb><panel>3</panel><colour>Orange</colour><date>%s</date><success>%s</success><difficulty>%s</difficulty><nice>%s</nice><notes>%s</notes></climb>",
		$datestr, $succ1, $diff1, $nice1, $notes1);
	$xml .= sprintf ("<climb><panel>3</panel><colour>Purple/White</colour><date>%s</date><success>%s</success><difficulty>%s</difficulty><nice>%s</nice><notes>%s</notes></climb>",
		$datestr, $succ2, $diff2, $nice2, $notes2);
	$xml .= sprintf ("<climb><panel>4</panel><colour>Purple</colour><date>%s</date><success>%s</success><difficulty>%s</difficulty><nice>%s</nice><notes>%s</notes></climb>",
		$datestr, $succ3, $diff3, $nice3, $notes3);

	$xml .= "</list>";

	return $xml;
}

function test_data()
{
	global $_GET;

	if (0) {
		$_GET['action']  = "add";
		$_GET['date']    = '2 days ago';
		$_GET['climbs']  = "3 or(d) p/w(dn), blu(mf)";
		//$_GET['climbs']  = "46 pw(d), blu, bg(2f), fe(f)";
		//$_GET['climbs']  = "32 all(d)";
		//$_GET['climbs']  = "71 all";
	} else {
		$_GET['action']  = "save";
		$_GET['climb_xml']  = get_random_climbs();
		$_GET['climber'] = "Rich Russon";
	}

}

function test_parse()
{
	$c = array ("pw", "rd (c)", "ti (s) ", "f(f) ", "tq (d)", "gray ( 1r)", "pk ( 1f  )", "r/w ( n)", "y ( mf  ) ", "all", "all(d)");

	printf ("abbr        colour        success     nice    notes\n");
	foreach ($c as $climb) {
		$p = climb_parse_climb ($xml, $climb);
		printf ("%-12s%-14s%-12s%-8s%s\n", $climb, $p['colour'], $p['success'], $p['nice'], $p['notes']);
	}
}


