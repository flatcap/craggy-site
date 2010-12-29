<?php

include_once 'geshi.php';

$file = $_GET['file'];

$geshi = new GeSHi();
$geshi->enable_keyword_links(false);
//$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);
$geshi->load_from_file($file);

echo $geshi->parse_code();

