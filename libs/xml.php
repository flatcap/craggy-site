<?php

function xml_new_string ($type)
{
	return new SimpleXMLElement ("<$type />");
}

function xml_get_attributes ($xml)
{
	$attrs = array();
	foreach($xml->attributes() as $a => $b) {
		    $attrs[$a] = (string) $b;
	}

	return $attrs;
}

function xml_add_error (&$xml, $message)
{
	$xml->addChild ('error', $message);
}

