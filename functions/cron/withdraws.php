<?php

if(php_sapi_name()=="cli") {

	chdir('../../');
	include('config.php');
	include('loader.php');
	$provider_initialize = 0;

	$db_fetch_withdraws = pg_fetch_all(pg_query("SELECT * FROM withdraws WHERE status = '1'"));

	foreach($db_fetch_withdraws as $db_fetch_withdraw) {

		if($provider_initialize == 0) { include('functions/payment_providers/'.$payment_provider.'.php'); $provider_initialize = 1; }

		$id_user = $db_fetch_withdraw['id_user'];
		$get_user_balance = pg_fetch_array(pg_query("SELECT balance FROM users WHERE id = $id_user"))['balance'];

		if($get_user_balance >= $db_fetch_withdraw['sats']) {
			$result = payinvoice($db_fetch_withdraw['lninvoice']);
		} else {
			$result['error'] = 1; $result['code'] = 999; $result['message'] = "Too low account balance to withdraw funds";
		}

		if(!empty($result['error'])) {
			// Transaction failed successfully! :D
			$status = '[Code '.$result['code'].'] - '.$result['message'];
			$id = $db_fetch_withdraw['id'];
			pg_query("UPDATE withdraws SET status = '$status' WHERE id = $id");
		} else {
			$status = "OK";
			$id = $db_fetch_withdraw['id'];
			$sats = $db_fetch_withdraw['sats'];

			pg_query("UPDATE users SET balance = balance - $sats WHERE id = $id_user");
			pg_query("UPDATE withdraws SET status = '$status' WHERE id = $id");
		}

	}

}