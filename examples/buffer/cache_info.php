<?php

apc_clear_cache ('user');

$jim = 'a:272:{i:1;a:4:{s:2:"id";s:1:"1";s:5:"panel";s:1:"1";s:6:"colour";s:6:"Yellow";s:5:"grade";s:2:"5+";}';

printf ("jim = %d\n", strlen ($jim));

for ($i = 0; $i < 10; $i++) {
	apc_store ("rich_key$i", $jim);
	//$ci = apc_cache_info('user');
	//printf ("num_slots = %d\n", $ci['num_slots']);
	//printf ("ttl = %d\n", $ci['ttl']);
	//printf ("num_hits = %d\n", $ci['num_hits']);
	//printf ("num_misses = %d\n", $ci['num_misses']);
	//printf ("num_inserts = %d\n", $ci['num_inserts']);
	//printf ("expunges = %d\n", $ci['expunges']);
	//printf ("start_time = %d\n", $ci['start_time']);
	//printf ("mem_size = %d\n", $ci['mem_size']);
	//printf ("num_entries = %d\n", $ci['num_entries']);
	//printf ("file_upload_progress = %d\n", $ci['file_upload_progress']);
	//printf ("cache_list = %d\n", $ci['cache_list']);
	//printf ("\n");
}

$ci = apc_cache_info('user');
printf ("User cache keys:\n");
foreach ($ci['cache_list'] as $item) {
	printf ("\t%s\n", $item['info']);
}

$ci = apc_cache_info();
printf ("System cache keys:\n");
foreach ($ci['cache_list'] as $item) {
	printf ("\t%s\n", $item['filename']);
}

