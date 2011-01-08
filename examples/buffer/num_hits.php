<?php

apc_clear_cache ('user');

apc_store ('wibble',   'some wibble data');
apc_store ('hatstand', 'other hatstand info');

sleep (3);

apc_store ('wibble', 'new wibble');
apc_fetch ('wibble');
apc_fetch ('wibble');
apc_fetch ('wibble');
apc_fetch ('hatstand');

sleep (2);

echo "<pre>\n";

$ci = apc_cache_info('user');
printf ("User cache keys:\n");
foreach ($ci['cache_list'] as $item) {
	printf ("\t%s\n", $item['info']);
}
printf ("\n");

$ci = apc_cache_info('user');
printf ("num_inserts = %d\n", $ci['num_inserts']);
printf ("num_hits = %d\n",    $ci['num_hits']);
printf ("\n");

$cl = $ci['cache_list'];

$w = $cl[0];
$h = $cl[1];

printf ("wibble\n");
printf ("\tnum_hits      = %s\n", $w['num_hits']);
printf ("\tmtime         = %s\n", $w['mtime']);
printf ("\tcreation_time = %s\n", $w['creation_time']);
printf ("\tdeletion_time = %s\n", $w['deletion_time']);
printf ("\taccess_time   = %s\n", $w['access_time']);
printf ("\n");

printf ("hatstand\n");
printf ("\tnum_hits      = %s\n", $h['num_hits']);
printf ("\tmtime         = %s\n", $h['mtime']);
printf ("\tcreation_time = %s\n", $h['creation_time']);
printf ("\tdeletion_time = %s\n", $h['deletion_time']);
printf ("\taccess_time   = %s\n", $h['access_time']);
printf ("\n");

var_dump ($ci);

