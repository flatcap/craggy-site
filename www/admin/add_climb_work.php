<?php

set_include_path ('../../libs');

//include 'db.php';
//include 'utils.php';
include 'colour.php';

//$_GET['data'] = "46 pw, blu, bg";
$_GET['data'] = "34 rd,gn,fp";

if (!isset ($_GET)) {
	echo "no get";
	return;
}

if (!array_key_exists ('data', $_GET)) {
	echo "no data";
	return;
}

$data=$_GET['data'];
$len = strlen ($data);
$data=trim ($data);
if (empty ($data)) {
	echo "empty data";
	return;
}

$parts = preg_split('/[\s,]+/', $data);
$panel = array_shift ($parts);
foreach ($parts as $key => $p) {
	if ($p == '') {
		unset ($parts[$key]);
	}
}

if (!is_numeric ($panel)) {
	printf ("Not a panel name: '%s'", $panel);
	return;
}

$list = new SimpleXMLElement ("<?xml-stylesheet type='text/xsl' href='route.xsl'?><list />");
$list->addAttribute ('type', 'climb');

foreach ($parts as $colour) {
	$climb = $list->addChild ('route');
	$climb->addChild ('panel', $panel);

	$c = colour_match ($colour);
	if ($c !== null) {
		$climb->addAttribute ('result', 'valid');
		$climb->addChild ('colour', $c['colour']);
	} else {
		$climb->addAttribute ('result', 'invalid');
		$climb->addChild ('colour', $colour);
		$climb->addChild ('notes', sprintf ("%s is not a valid colour", $colour));
	}

	$climb->addChild ('grade', '6a+');			// grade
	$climb->addChild ('success', 'clean');			// success
	$climb->addChild ('difficulty', 'medium');		// difficulty
	$climb->addChild ('climb_type', 'Top Rope');		// type
	$climb->addChild ('nice', 'N');				// nice
	$climb->addChild ('date', '2011-02-05');		// date_climbed
	$climb->addChild ('climb_notes', 'tricky');		// notes - climb_notes
	//$climb->addChild ('errors', 'x is not y');		// notes - error messages

}

header('Content-Type: application/xml; charset=ISO-8859-1');
echo $list->asXML();

