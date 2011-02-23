<?php

$text = file_get_contents('php://input');

$xml = simplexml_load_string ($text);

$value = urldecode ($xml->text[0]);

header('Content-Type: application/xml; charset=ISO-8859-1');

$reply = sprintf ("Reply: this is the original -->%s<!--", $value);
$xml->text[0] = $reply;

echo $xml->asXML();
