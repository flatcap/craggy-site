<?php

static $fd = null;

function log_init ($path = null, $verbose = false)
{
	global $fd;

	if ($path)
		ini_set('error_log', $path);
	ini_set('display_errors', 0);
	ini_set('log_errors', 1);
	libxml_use_internal_errors (true);

	$fd = fopen ($path, "a");

	if ($verbose)
		log_string ("Log started: " . date ('Y/m/d H:i:s'));
}

function log_var ($var)
{
	global $fd;
	$str = print_r ($var, true);
	fwrite ($fd, $str);
	if ($str[strlen ($str) - 1] != "\n")
		fwrite ($fd, "\n");
}

function log_string ($str)
{
	global $fd;
	fwrite ($fd, $str);
	if ($str[strlen ($str) - 1] != "\n")
		fwrite ($fd, "\n");
}

