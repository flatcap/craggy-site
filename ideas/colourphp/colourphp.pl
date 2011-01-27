#! /usr/bin/perl -w

use Term::ANSIColor;
use IPC::Open3;

sub srcscan
{
# Usage: srcscan($text, $normalColor)
#    $text -- the text to colorize
#    $normalColor -- The escape sequence to use for non-source text.

# Looks for text between ` and ', and colors it srcColor.

	my($line, $normalColor) = @_;

	my($srcon) = color("reset") . $colors{"srcColor"};
	my($srcoff) = color("reset") . $normalColor;

	$line = $normalColor . $line;

	# This substitute replaces `foo' with `AfooB' where A is the escape
	# sequence that turns on the the desired source color, and B is the
	# escape sequence that returns to $normalColor.
	#$line =~ s/\‘(.*?)\’/\`$srcon$1$srcoff\'/g;
	#$line =~ s/(.*): (In function) ‘(.*?)’:/$1: $2 ‘$srcon$3$srcoff’:/g;
	#$line =~ s/‘(.*)’:/‘wibble’/g;
	#$line =~ s/(.*)/wibble:$1\n/g;
	#$line =~ s/(In function) ‘(.*?)’:/$1 ‘$srcon$2$srcoff’:/g;
	#$line =~ s/(In function) ‘(.*?)’:/$1 ‘$srcon$2$srcoff’:/g;
	#PHP Warning:  simplexml_load_string(): Entity: line 1: parser error : Start tag expected, '<' not found in /home/flatcap/downloads/craggy/www/admin/add_work.php on line 298
	#$line =~ s/(.*):/wibble/g;

#	app.c: In function ‘rich_get_ring’:
#	app.c:84: warning: unused variable ‘ring’

	print($line, color("reset"));
}

#
# ------------------------------------------------------------------------------
#

$colors{"srcColor"} = color("cyan");
$colors{"introColor"} = color("blue");

$colors{"warningFileNameColor"} = color("yellow");
$colors{"warningNumberColor"}   = color("yellow");
$colors{"warningMessageColor"}  = color("yellow");

$colors{"errorFileNameColor"} = color("bold red");
$colors{"errorNumberColor"}   = color("bold red");
$colors{"errorMessageColor"}  = color("bold red");

#$compiler = "/usr/bin/php";
$compiler = "./error.sh";

# Keep the pid of the compiler process so we can get its return
# code and use that as our return code.
$compiler_pid = open3('<&STDIN', \*GCCOUT, '', $compiler, @ARGV);

# State variables
$php_stack  = 0;
$call_stack = 0;
$empty_line = 0;

# Colorize the output from the compiler.
while(<GCCOUT>)
{
	if (m/^$/) {
		if (!$empty_line) {
			print ("\n");
			$empty_line = 1;
			next;
		}
	}

	$empty_line = 0;

	if (m/^(.*?):([0-9]+)[:,](.*)$/) # filename:lineno:message
	{
		$field1 = $1 || "";
		$field2 = $2 || "";
		$field3 = $3 || "";

		if ($field3 =~ m/\s+warning:.*/)
		{
			# Warning
			print($colors{"warningFileNameColor"}, "$field1:", color("reset"));
			print($colors{"warningNumberColor"}, "$field2:", color("reset"));
			srcscan($field3, $colors{"warningMessageColor"});
		}
		else
		{
			# Error
			print($colors{"errorFileNameColor"}, "$field1:", color("reset"));
			print($colors{"errorNumberColor"}, "$field2:", color("reset"));
			srcscan($field3, $colors{"errorMessageColor"});
		}
		print("\n");
	}
	elsif (m/^(.*?):(.+):$/) # filename:message:
	{
		# No line number, treat as an "introductory" line of text.
		srcscan($2, $colors{"introColor"});
	}
	# PHP Warning:  simplexml_load_string(): Entity: line 1: parser error : Start tag expected, '<' not found in /home/flatcap/downloads/craggy/www/admin/add_work.php on line 298
	#elsif (m/^(PHP Warning):\s+(.*)/) # filename:message:
	elsif (m/^(PHP)(.*)/) # filename:message:
	{
		$field1 = $1 || "";
		$field2 = $2 || "";
		$field3 = $3 || "";

		# Warning
		#print($colors{"warningFileNameColor"}, "$field1: ", color("reset"));
		#srcscan($field2, $colors{"warningMessageColor"});
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

# Get the return code of the compiler and exit with that.
waitpid($compiler_pid, 0);
exit ($? >> 8);





