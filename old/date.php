<?php

$s = "7 dec";
$s = "2007-12-07";
$s = "today";
$s = "yesterday";
$s = "last Friday";
$s = "2 days ago";

//$d = date_create($s);
//echo date_format ($d, "\nD M j G:i:s T Y\n\n");

$d = strtotime($s);
echo strftime("%n %l:%M %p  %A %-d %B%n%n", $d);

?>
