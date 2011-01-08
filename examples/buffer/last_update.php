<?php

set_include_path ('../../libs');

include 'db.php';
include 'db_names.php';

date_default_timezone_set('UTC');

$db = db_get_database();
$query = "select value from $DB_DATA where name = 'last_update'";

$time_start = microtime (true);
$result = mysql_query($query);
$time_end   = microtime (true);

$row = mysql_fetch_array($result, MYSQL_ASSOC);
mysql_free_result($result);

$diff = $time_end - $time_start;

printf ("result = %s, elapsed = %.6f (1/%.0f) seconds\n", $row['value'], $diff, 1/$diff);

