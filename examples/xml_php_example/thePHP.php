<?php

/*
Assumes you have connected to your database and that you have a table
called 'people' with 3 columns - title, firstname, surname
*/

$query = 'SELECT title,firstname,surname FROM people';
$result = mysql_query($query);

$xml = '';

while($array = mysql_fetch_array($result)) {

	$title = $array['title'];
	$firstname = $array['firstname'];
	$surname = $array['surname'];

	$xml .= "\t\t<person>\n";
	$xml .= "\t\t\t<firstname title=\"$title\">$firstname</firstname>\n";
	$xml .= "\t\t\t<surname>$surname</surname>\n";
	$xml .= "\t\t</person>\n";

}


header('Content-Type: application/xml; charset=ISO-8859-1');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
echo "\t<people>\n";
echo $xml;
echo "\t</people>\n";


?>
