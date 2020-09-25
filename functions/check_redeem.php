<?php

function is_redeemed($id) {

	$check = file('https://www.humblebundle.com/gift?key='.$id);
	$count = count($check)-1;
	$return['title'] = '---';

	for($i=0; $i<=$count; $i++) {

		if (strpos($check[$i], 'used-gift') !== false) {
			// Used gift
			$return['value'] = 1;
			return $return;
		} else if (strpos($check[$i], 'giftName') !== false) {
			// Good gift
			$return['value'] = 0;

			for($j=0; $j<=$count; $j++) {

				if (strpos($check[$j], 'giftGameKey') !== false) {
					$return['title'] = json_decode($check[$j], TRUE)['giftName'];
					return $return;
				}
			}
		}
	}

	if(empty($return['value'])) {
		// Unknown error - probably key is invalid
		$return['value'] = 999;
		return $return;
	}
}
