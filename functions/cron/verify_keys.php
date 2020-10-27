<?php

if(php_sapi_name()=="cli") {

	chdir('../../');
	include('config.php');
	include('loader.php');
	include('functions/check_redeem.php');
	include('functions/id_regexp_check.php');
	include('functions/encryption.php');

	$db_fetch_keys_to_verify = pg_fetch_all(pg_query("SELECT id, key, title FROM games WHERE status = 900 LIMIT 1"));
	if(empty($db_fetch_keys_to_verify)) { die(); }

	foreach($db_fetch_keys_to_verify as $db_key_to_verify) {

		$id = decrypt($db_key_to_verify['key'], $hashed_db_password, $iv);
		$key = $db_key_to_verify['key'];

		if(id_verify($id)==true) {

			$check_redeem = is_redeemed($id);
			if($check_redeem['value'] == 0) {
				// Key works!
				$status = 0;
				$title = base64_encode(trim($check_redeem['title']));
			} else if ($check_redeem['value'] == 1) {
				$status = 997;
			} else {
				$status = 998;
			}
		} else {
				$status = 999;
		}

	pg_query("UPDATE games SET status = $status WHERE status = 900 AND key = '$key' ");
	if($status == 0) { pg_query("UPDATE games SET title = '$title' WHERE key = '$key' "); }

	}
}

?>