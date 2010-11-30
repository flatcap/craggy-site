<?php

if (isset ($argc)) {
	if ($argc < 2) {
		printf ("args\n");
		return;
	}
	array_shift ($argv);
	$q = implode (" ", $argv);
} else {
	if (!isset ($_GET)) {
		//echo "bad call: no get";
		return;
	}
		
	if (!array_key_exists ('q', $_GET)) {
		//echo "bad call: no q";
		return;
	}

	$q=trim ($_GET["q"]);
	if (empty ($q)) {
		//echo "empty";
		return;
	}
}

$date = strtotime ($q);
$now  = strtotime ("now");

if ($date == FALSE)
	$result = "Invalid date";
else if ($date > $now)
	$result = "Date cannot be in the future";
else
	$result = date ("D j M Y", $date);

printf ("%s (%s)", $q, $result);
if (isset ($argc))
	printf ("\n");
