<?php

set_include_path ('../../libs');

include 'log.php';
include 'xml.php';

function lookup_main()
{
	global $HTTP_RAW_POST_DATA;

	if (!isset ($HTTP_RAW_POST_DATA)) {
		$xml = xml_new_string ('validation');
		$xml->addAttribute ('type', 'unknown');
		$msg = "no HTTP_RAW_POST_DATA";
		log_string ($msg);
		return $xml;
	}

	$xml = simplexml_load_string ($HTTP_RAW_POST_DATA);
	$errlist = libxml_get_errors();
	if (count ($errlist)) {
		$xml = xml_new_string ('validation');
		$xml->addAttribute ('type', 'unknown');
		foreach ($errlist as $error) {
			$xml->addChild ('error', $error->message);
			log_var ($error);
		}
		return $xml;
	}

	$attrs = xml_get_attributes ($xml[0]);
	if (!array_key_exists ('type', $attrs)) {
		$msg = "no type in xml";
		log_string ($msg);
		$xml->addChild ('error', $msg);
		return $xml;
	}

	$type = $attrs['type'];
	switch ($type) {
		case 'colour':
			include 'colour.php';
			colour_match_xml ($xml);
			break;
		case 'date':
			include 'date.php';
			date_match_xml ($xml);
			break;
		case 'grade':
			include 'grade.php';
			grade_match_xml ($xml);
			break;
		case 'nice':
			include 'nice.php';
			nice_match_xml ($xml);
			break;
		case 'panel':
			include 'panel.php';
			panel_match_xml ($xml);
			break;
		case 'setter':
			include 'setter.php';
			setter_match_xml ($xml);
			break;
		case 'colour':
			include 'colour.php';
			colour_match_xml ($xml);
			break;
		case 'difficulty':
			include 'difficulty.php';
			difficulty_match_xml ($xml);
			break;
		case 'success':
			include 'success.php';
			success_match_xml ($xml);
			break;
		default:
			$msg = "unknown type: $type";
			log_string ($msg);
			$xml->addChild ('error', $msg);
	}

	return $xml;
}


log_init ('/dev/pts/8');

// We're going to reply in xml, regardless of the input
header('Content-Type: application/xml; charset=ISO-8859-1');

$xml = lookup_main();

echo $xml->asXML();
log_var ($xml);

