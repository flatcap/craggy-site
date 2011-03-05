<?php

function log_init ($path = null)
{
	if ($path)
		ini_set('error_log', $path);
	ini_set('display_errors', 0);
	ini_set('log_errors', 1);
	libxml_use_internal_errors (true);
}

function log_var ($var)
{
	trigger_error (sprintf ("%s", print_r ($var, true)));
}

function log_string ($str)
{
	trigger_error ($str);
}


