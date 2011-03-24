<?php

static $fd = null;

function log_init ($path = null)
{
	global $fd;

	if ($path)
		ini_set('error_log', $path);
	ini_set('display_errors', 0);
	ini_set('log_errors', 1);
	libxml_use_internal_errors (true);

	$fd = fopen ($path, "a");
}

function log_var ($var)
{
	global $fd;
	fwrite ($fd, print_r ($var, true));
}

function log_string ($str)
{
	global $fd;
	fwrite ($fd, $str);
}


