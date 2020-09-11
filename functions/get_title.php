<?php

function getTitle($key) {
	$key_data = file('https://www.humblebundle.com/gift?key='.$key);
	$count = count($key_data)-1;
	
	for($i=0; $i<=$count; $i++) {
		if (strpos($key_data[$i], 'giftGameKey') !== false) {
		return json_decode($key_data[$i], TRUE)['giftName'];
		}
	}

// This means that for some reason the data could not be read. Usually it means that the code was used or Humble Bundle does not work.
return 999;
}