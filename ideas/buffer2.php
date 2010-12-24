<?php
$fruit  = 'apple';
$veggie = 'carrot';

apc_clear_cache('user');

//apc_store('foo', $fruit);
//apc_store('bar', $veggie);

//apc_delete('foo');
//apc_delete('bar');

if (apc_exists('foo')) {
	echo "Foo exists: ";
	echo apc_fetch('foo');
} else {
	echo "Foo does not exist";
}

echo PHP_EOL;
if (apc_exists('baz')) {
	echo "Baz exists.";
} else {
	echo "Baz does not exist";
}

echo PHP_EOL;

$ret = apc_exists(array('foo', 'donotexist', 'bar'));
var_dump($ret);

?>
