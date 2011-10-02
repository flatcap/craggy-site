<?php

set_include_path ('../../libs');

include_once 'xml.php';

function lookup_main()
{
	global $HTTP_RAW_POST_DATA;

	if (!isset ($HTTP_RAW_POST_DATA)) {
		$xml = xml_new_string ('validation');
		$xml->addAttribute ('type', 'unknown');
		$msg = "no HTTP_RAW_POST_DATA";
		return $xml;
	}

	$xml = simplexml_load_string ($HTTP_RAW_POST_DATA);
	$errlist = libxml_get_errors();
	if (count ($errlist)) {
		$xml = xml_new_string ('validation');
		$xml->addAttribute ('type', 'unknown');
		foreach ($errlist as $error) {
			$xml->addChild ('error', $error->message);
		}
		return $xml;
	}

	$attrs = xml_get_attributes ($xml[0]);
	if (!array_key_exists ('type', $attrs)) {
		$msg = "no type in xml";
		$xml->addChild ('error', $msg);
		return $xml;
	}

	$type = $attrs['type'];
	switch ($type) {
		case 'climber':    include_once 'climber.php';    climber_match_xml    ($xml, $xml->input); break;
		case 'climb_type': include_once 'climb_type.php'; climb_type_match_xml ($xml, $xml->input); break;
		case 'colour':     include_once 'colour.php';     colour_match_xml     ($xml, $xml->input); break;
		case 'date':       include_once 'date.php';       date_match_xml       ($xml, $xml->input); break;
		case 'difficulty': include_once 'difficulty.php'; difficulty_match_xml ($xml, $xml->input); break;
		case 'grade':      include_once 'grade.php';      grade_match_xml      ($xml, $xml->input); break;
		case 'height':     include_once 'height.php';     height_match_xml     ($xml, $xml->input); break;
		case 'nice':       include_once 'nice.php';       nice_match_xml       ($xml, $xml->input); break;
		case 'number':     include_once 'number.php';     number_match_xml     ($xml, $xml->input); break;
		case 'panel':      include_once 'panel.php';      panel_match_xml      ($xml, $xml->input); break;
		case 'setter':     include_once 'setter.php';     setter_match_xml     ($xml, $xml->input); break;
		case 'success':    include_once 'success.php';    success_match_xml    ($xml, $xml->input); break;
		case 'taglist':    include_once 'taglist.php';    taglist_match_xml    ($xml, $xml->input); break;
		default:
			$msg = "unknown type: $type";
			$xml->addChild ('error', $msg);
	}

	return $xml;
}


// We're going to reply in xml, regardless of the input
header('Content-Type: application/xml; charset=ISO-8859-1');

$xml = lookup_main();

echo $xml->asXML();

