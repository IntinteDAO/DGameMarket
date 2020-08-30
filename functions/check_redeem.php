<?php

function is_redeemed($id) {

	$check = file('https://www.humblebundle.com/gift?key='.$id);
	$count = count($check)-1;

	for($i=0; $i<=$count; $i++) {

		if (strpos($check[$i], 'used-gift') !== false) {
		return 1; }
		else if (strpos($check[$i], 'giftName') !== false) {
		return 0; }

	}

	return 999;
}