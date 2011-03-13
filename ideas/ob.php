<?php
// buffer the output
ob_start();
for ($j=1; $j <= 5; $j++) {

	list($usec, $sec) = explode(" ",microtime());
	$debut[$j] = ((float)$usec + (float)$sec);

	echo str_repeat("0123456789",5000) . '<br>' ;

	list($usec, $sec) = explode(" ",microtime());
	$fin[$j] = ((float)$usec + (float)$sec);
}

for ($j=1; $j <= 5; $j++) {
	 echo round($fin[$j]-$debut[$j], 5) . '<br>';
}
ob_flush();
?>
