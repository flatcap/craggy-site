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


