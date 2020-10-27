<?php


if(php_sapi_name()=="cli") {

	chdir('../../');
	include('config.php');
	include('loader.php');
	include('functions/check_redeem.php');
	include('functions/encryption.php');
	$provider_initialize = 0;

	$time = time();
	$db_fetch_invoices = pg_fetch_all(pg_query("SELECT id, id_game, buyer, price, fee, invoice, status FROM invoices WHERE (timestamp + $expire_buy_time) > $time"));
	if(empty($db_fetch_invoices)) { die(); }

	foreach($db_fetch_invoices as $db_fetch_invoice) {

		$id = $db_fetch_invoice['id'];
		$game_id = $db_fetch_invoice['id_game'];
		$invoice = $db_fetch_invoice['invoice'];
		$price = $db_fetch_invoice['price'];
		$game_fee = $db_fetch_invoice['fee'];
		$get_key = pg_fetch_array(pg_query("SELECT key FROM games WHERE id = $game_id"))['key'];
		$decode_get_key = decrypt($get_key, $hashed_db_password, $iv);

		if($db_fetch_invoice['status'] == 0) {

			// Step 1 - Check if the game is still available on Humble Bundle
			$verification = is_redeemed($decode_get_key);

			if($verification['value'] == 0) {
				pg_query("UPDATE invoices SET status = 1 WHERE id = $id");
				if($provider_initialize == 0) { include('functions/payment_providers/'.$payment_provider.'.php'); $provider_initialize = 1; }
				$full_price = $price + $game_fee;
				$buyer = $db_fetch_invoice['buyer'];
				if($buyer != 0) { $invoice = create_invoice($full_price, "$buyer"); } else { $invoice = create_invoice($full_price, "anonymous"); }
				pg_query("UPDATE invoices SET invoice = '$invoice' WHERE id = $id");
				echo 'Verification 1 - Updated, game code works.'.PHP_EOL;
			} else {
				pg_query("UPDATE invoices SET status = 999 WHERE id = $id");
				pg_query("UPDATE games SET status = 996 WHERE id = $game_id");
				echo 'Verification 1 - Updated, game code failed.'.PHP_EOL;
			}

		} else if($db_fetch_invoice['status'] == 1) {

			// Step 3 - Verify payment
			if($provider_initialize == 0) { include('functions/payment_providers/'.$payment_provider.'.php'); $provider_initialize = 1; }
			$get_all_invoices = get_all_invoices();
			$count_invoices = count($get_all_invoices)-1;
			for($i=0; $i<=$count_invoices; $i++) {

				if($get_all_invoices[$i]['pay_req'] == $invoice && !empty($get_all_invoices[$i]['ispaid'])) {
					// Payment completed
					pg_query("UPDATE invoices SET status = 2 WHERE id = $id");
				}

			}
		} else if($db_fetch_invoice['status'] == 2) {

			// Step 4 - Verify key again
			$verification = is_redeemed($decode_get_key);

			if($verification['value'] == 0) {
				$buyer = $db_fetch_invoice['buyer'];
				pg_query("UPDATE games SET status = 3 WHERE (id = $game_id)");
				pg_query("UPDATE games SET id_buyer = $buyer WHERE id = $game_id");
				pg_query("UPDATE invoices SET status = 3 WHERE id = $id");
				$id_seller = pg_fetch_array(pg_query("SELECT id_seller FROM games WHERE id = $game_id"))['id_seller'];
				pg_query("UPDATE users SET balance = balance + $price WHERE id = $id_seller");
				echo 'Verification 4 - Updated, game code works.'.PHP_EOL;
			} else {

				if($buyer != 0) {
					pg_query("UPDATE invoices SET status = 998 WHERE id = $id");
					pg_query("UPDATE users SET balance = balance + $price WHERE id = $buyer");
				} else {
					pg_query("UPDATE invoices SET status = 997 WHERE id = $id");
				}

				echo 'Verification 4 - Updated, game code failed, chargeback'.PHP_EOL;
			}

		}



	}
}

?>