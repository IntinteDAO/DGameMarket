<?php

if(php_sapi_name()=="cli") {

	chdir('../../');
	include('config.php');
	include('loader.php');
	$provider_initialize = 0;

	$db_fetch_withdraws = $sqlite_withdraws_db->query("SELECT * FROM withdraws WHERE status = 1");

	while($db_fetch_withdraw = $db_fetch_withdraws->fetchArray(SQLITE3_ASSOC)) {

		if($provider_initialize == 0) { include('functions/payment_providers/'.$payment_provider.'.php'); $provider_initialize = 1; }
		$result = payinvoice($db_fetch_withdraw['lninvoice']);

		if(!empty($result['error'])) {
			// Transaction failed successully! :D
			$status = '[Code '.$result['code'].'] - '.$result['message'];
			$id = $db_fetch_withdraw['id'];
			$sqlite_withdraws_db->query("UPDATE withdraws SET status = '$status' WHERE id = $id");
		} else {
			$status = "OK";
			$id = $db_fetch_withdraw['id'];
			$sats = $db_fetch_withdraw['sats'];
			$id_user = $db_fetch_withdraw['id_user'];
			$sqlite_users_db->query("UPDATE users SET balance = balance - $sats WHERE id = $id_user");
			$sqlite_withdraws_db->query("UPDATE withdraws SET status = '$status' WHERE id = $id");
		}

	}

}