<?php

include('config.php');
include('header.php');
include('template/navbar.php');
include('template/add_keys.html');
include('functions/check_redeem.php');
include('functions/get_title.php');
include('functions/id_regexp_check.php');

if(!empty($_POST['keys'])) {

$keys = $_POST['keys'];
$keys = explode("\n", htmlspecialchars($_POST['keys']));

	for($i=0; $i<=count($keys)-1; $i++) {
		$id = str_replace('https://www.humblebundle.com/gift?key=', '', $keys[$i]);
		$id = trim($id);

		if(id_verify($id)==true) {
			$check_redeem = is_redeemed($id);

			if($check_redeem == 0) {
				echo 'Key is ok! - '.getTitle($id).'<br>';
			} else if ($check_redeem == 1) {
				echo 'Key is used!'.'<br>';
			} else {
				echo 'Key is probably bad!'.$id.'<br>';
			}
		} else {
				echo 'Wrong ID<br>';
		}
	}

}

include('footer.php');
