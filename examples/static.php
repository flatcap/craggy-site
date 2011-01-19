<?php

function test()
{
	static $a = 0;
	echo "$a\n";
	$a++;
}


test();
test();
test();

