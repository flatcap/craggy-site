#! /usr/bin/perl -w

use Term::ANSIColor;
use IPC::Open3;

# State variables
#$php_stack  = 0;
$call_stack = 0;
$empty_line = 0;

while(<>)
{
	# Ignore PHP lines
	if (m/^PHP.*/) {
		next;
	}

	# Squash empty lines
	if (m/^$/) {
		$call_stack = 0;
		if (!$empty_line) {
			$empty_line = 1;
			next;
		}
	}

	$empty_line = 0;

	if (m/(.*)\/home\/flatcap\/downloads\/craggy\/([^ ]+)(.*)\n/) {
		print ("$1", color('red'), "$2", color('reset'), "$3", "\n");
		next;
		#s!/home/flatcap/downloads/craggy/!!;
	}

	# shorten paths

	if ($call_stack) {
		s/^\s+[0-9\.]+\s+[0-9]+\s+/\t/;
		print (color('cyan'), "$_", color('reset'));
		next;
	}

	# call stack
	if (m/^(Call Stack:)$/) {
		print (color('cyan'), "$1", color('reset'), "\n");
		if (!$call_stack) {
			$call_stack = 1;
			next;
		}
	}

	$call_stack = 0;

	if (m/^(.*?):([0-9]+)[:,](.*)$/) # filename:lineno:message
	{
		$field1 = $1 || "";
		$field2 = $2 || "";
		$field3 = $3 || "";

		if ($field3 =~ m/\s+warning:.*/)
		{
			# Warning
			print(color("red"), "$field1:", color("reset"));
			print(color("blue"), "$field2:", color("reset"));
			srcscan($field3, color("green"));
		}
		else
		{
			# Error
			print(color("cyan"), "$field1:", color("reset"));
			print(color("yellow"), "$field2:", color("reset"));
			srcscan($field3, color("red"));
		}
		print("\n");
	}
	elsif (m/^(.*?):(.+):$/) # filename:message:
	{
		# No line number, treat as an "introductory" line of text.
		srcscan($2, color("red"));
	}
	# PHP Warning:  simplexml_load_string(): Entity: line 1: parser error : Start tag expected, '<' not found in /home/flatcap/downloads/craggy/www/admin/add_work.php on line 298
	#elsif (m/^(PHP Warning):\s+(.*)/) # filename:message:
	elsif (m/^(PHP)(.*)/) # filename:message:
	{
		$field1 = $1 || "";
		$field2 = $2 || "";
		$field3 = $3 || "";

		# Warning
		#print(color("red"), "$field1: ", color("reset"));
		#srcscan($field2, color("yellow"));
		#print("\n");
	}
	elsif ((m/^Warning:/) || (m/^Fatal/))
	{
		print("\n");
		print(color("reset"), $_);
	}
	elsif (m/^$/) # empty line
	{
		# do nothing, for now
	}
	else # Anything else.
	{
		# Doesn't seem to be a warning or an error. Print normally.
		print(color("reset"), $_);
	}
}

