<?php

include "db.php";
include "utils.php";

function rss_get_routes()
{
	$table   = "v_route";
	$columns = array ("panel", "colour", "grade", "date_set");
	$where   = NULL;
	$order   = "date_set desc";

	$list = db_select($table, $columns, $where, $order);

	return $list;
}

function rss_recent_records($routes)
{
	$records   = array();
	$route_set = NULL;
	$count     = 0;
	$date      = "";

	foreach ($routes as $row) {
		if ($row['date_set'] != $date) {
			$date = $row['date_set'];

			if (count ($records) >= 15)
				break;
			$route_set = array();
			$records[$date] = $route_set;
		}

		array_push ($records[$date], $row);
	}

	return $records;
}

function rss_render_xml ($records)
{
	$output = "";

	$output .= "<rss version='2.0' xmlns:atom='http://www.w3.org/2005/Atom'>\n";
	$output .= "<channel>\n";
	$output .= "\t<title>Craggy Island</title>\n";
	$output .= "\t<description>New routes set at Craggy</description>\n";
	$output .= "\t<link>http://craggy-island.com/</link>\n\n";
	$output .= "\t<copyright>Rich Russon</copyright>\n";
	$output .= "\t<atom:link href='http://flatcap.hopto.org/rss' rel='self' type='application/rss+xml' />\n";

	foreach ($records as $date => $routes) {
		$panels = array();
		$desc = "";

		$output .= "\t<item>\n";
		$desc = "<dl>";
		foreach ($routes as $route) {
			$p = $route['panel'];
			if (!array_key_exists ($p, $panels)) {
				$panels[$p] = $p;
				$desc .= "<dt>Panel: " . $p . "</dt>";
			}
			$desc .= "<dd>{$route['colour']} - {$route['grade']}</dd>";
		}
		$desc .= "</dl>";
		$desc = htmlentities ($desc);
		$title = "New routes on panel";
		if (count ($panels) > 1)
			$title .= "s";
		$title .= ": ";
		$title .= implode ($panels, ", ");

		$output .= "\t\t<title>$title</title>\n";
		$output .= "\t\t<pubDate>" . strftime( "%a, %d %b %Y 09:00:00 %Z", strtotime ($date)) . "</pubDate>\n";
		$output .= "\t\t<link>http://flatcap.hopto.org/craggy/routes.php</link>\n";
		$output .= "\t\t<guid>xyx_$date</guid>\n";
		$output .= "\t\t<description>$desc</description>\n";
		$output .= "\t</item>\n";
	}

	$output .= "</channel>\n";
	$output .= "</rss>\n";

	return $output;
}

function rss_main()
{
	$routes = rss_get_routes();
	$records = rss_recent_records($routes);
	$output = rss_render_xml($records);

	return $output;
}


date_default_timezone_set("GMT");

header('Content-type: text/xml');

echo rss_main ();

