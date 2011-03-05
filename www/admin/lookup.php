<?php

set_include_path ('../../libs');

include 'setter2.php';

function log_var ($var)
{
	trigger_error (sprintf ("%s", print_r ($var, true)));
}

function get_attributes ($xml)
{
	$attrs = array();
	foreach($xml->attributes() as $a => $b) {
		    $attrs[$a] = (string) $b;
	}

	return $attrs;
}


ini_set('error_log', '/dev/pts/8');
ini_set('display_errors', 0);
ini_set('log_errors', 1);
libxml_use_internal_errors (true);

// We're going to reply in xml, regardless of the input
header('Content-Type: application/xml; charset=ISO-8859-1');

$xml_reply = new SimpleXMLElement ("<validation />");
//$xml_reply->addAttribute ('type', 'setter');

//$text = file_get_contents('php://input');
//$text = file_get_contents('php://stdin');
//log_var ($text);

$xml_input = simplexml_load_string ($HTTP_RAW_POST_DATA);
$errlist = libxml_get_errors();
if (count ($errlist)) {
	foreach ($errlist as $error) {
		// handle errors here
		$xml_reply->addChild ('error', $error->message);
		log_var ($error);
	}
	echo htmlentities ($xml_reply->asXML());
	exit (1);
	//libxml_clear_errors();
}

$input  = (string) $xml_input->input;
//log_var ($input);
$attrs = get_attributes ($xml_input[0]);
$type   = $attrs['type'];
//log_var ($type);

$xml_reply->addChild ('input', $input);

$xml_reply->addAttribute ('type', $type);
//echo htmlentities ($xml_reply->asXML());

$setter = setter_match ($input);
if ($setter === null) {
	$xml_reply->addChild ('error', "no such setter");
} else {
	$name = trim ($setter['first_name'] . " " . $setter['surname']);
	$xml_reply->addChild ('setter', $name);
}

//log_var ($HTTP_RAW_POST_DATA);
//log_var ($xml_input);

echo $xml_reply->asXML();
log_var ($xml_reply->asXML());
exit (1);

$value = urldecode ($xml->text[0]);

$reply = sprintf ("Reply: this is the original -->%s<!--", $value);
$xml->text[0] = $reply;

/*
Input:
<validation type='colour'>
	<input>rd</input>
</validation>

Output:
<validation type='colour'>
	<colour>Red</colour>
	<input>rd</input>
	<error>Unknown colour</error>
</validation>
*/
