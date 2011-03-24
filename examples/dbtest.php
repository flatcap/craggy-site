<?php

set_include_path ("../libs");

include 'db.php';

date_default_timezone_set('UTC');

echo "<pre>\n";
$name = 'route';

// wake up db
$c = db_select ($name);

$time_start = microtime (true);

for ($i = 0; $i < 200; $i++)
	$c = db_select ($name);

$time_end   = microtime (true);
printf ("$name has %d entries\n", count ($c));

$diff = $time_end - $time_start;

printf ("elapsed = %.6f (1/%.0f) seconds\n", $diff, 1/$diff);

ob_flush();

