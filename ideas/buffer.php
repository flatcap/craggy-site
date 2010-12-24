<?php
function apc_fetch_udt($key){
	$g = apc_fetch($key);
	if ($g){
		list($udt,$val) = $g;
		if (get_last_modified_date()<$udt) {
			$val = unserialize($val);
			return $val;
		} else {
			apc_delete($key);
		}
	}
}
function apc_store_udt($key,$g){
	$udt = time();
	$g   = serialize($g);
	$apc = array($udt,$g);
	apc_store($key, $apc);
}

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

