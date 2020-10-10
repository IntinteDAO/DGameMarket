<?php

include("config.php");
include("loader.php");
include("functions/oracle.php");
include("functions/encryption.php");

if(!isset($_SESSION['login'])) {
	echo '<b>It is recommended to log in with your account!</b><br>';
	$buyer = 0;
} else {
	$buyer = $_SESSION['id'];
}

	$timestamp = time();
	$check_expired_time = $timestamp + $expire_buy_time;

if(isset($_GET['game_id'])) {

	// Get game ID, create DGM invoice
	$game_id = $_GET['game_id'];

	if(is_numeric($game_id) && $game_id > 0) {
		$db_game_data = $sqlite_games_db->querySingle("SELECT price, key FROM games WHERE (id = $game_id AND status=1)", true);

		if(!empty($db_game_data)) {

			$bitcoin_price = crypto_price('bitcoin');
			$random_id = trim(uniqid(md5(microtime())));
			$price = floor(((($db_game_data['price']) / 100000000 / 100) * $bitcoin_price) * 100000000);
			$fee = floor(((($fee) / 100000000 / 100) * $bitcoin_price) * 100000000);

			if($buyer != 0) {
				$is_already_added = $sqlite_invoices_db->querySingle("SELECT id_unique FROM invoices WHERE (id_game = $game_id AND buyer = $buyer AND timestamp < $check_expired_time)", true)['id'];
			}

			if(empty($is_already_added)) {
				$sqlite_invoices_db->querySingle("INSERT INTO invoices (id_game, id_unique, buyer, price, fee, invoice, timestamp, status) VALUES ($game_id, '$random_id', $buyer, $price, $fee, 0, 0, 0, $timestamp, 0)", true);
			}

			echo '<meta http-equiv="refresh" content="0; url=?id='.$random_id.'" />';
		} else {
			echo 'Wrong key';
		}

	} else {
		echo 'Wrong Game ID';
	}

} else if(isset($_GET['id'])) {

	if (preg_match("/^[a-f0-9]{45}$/", $_GET['id'])) {
		$id = trim($_GET['id']);
	} else {
		die('Wrong invoice ID');
	}

	$is_exists = $sqlite_invoices_db->querySingle("SELECT id_game, buyer, timestamp, id_unique, status, invoice, price, fee FROM invoices WHERE (random_id = '$id')", true);

		if(empty($is_exists)) { die("Sorry, this invoice ID is invalid!"); }
		if($is_exists['buyer'] != $buyer ) { die("Sorry, this invoice is not for you!"); }
		if((($is_exists['timestamp'] + $expire_buy_time) < $timestamp ) && $is_exists['status']<3) { die("Sorry, this invoice is expired!"); }
		echo 'Time to expire: '.($is_exists['timestamp'] - $timestamp + $expire_buy_time).'<br>';
		if($is_exists['status'] == 0) { echo 'Phase 1 / 4 - Verification of the game key.<br>'; }
		if($is_exists['status'] == 1) { echo 'Phase 2 / 4 - Creating Lightning Invoice.<br>'; }
		if($is_exists['status'] == 2) {

			echo 'Phase 3 / 4 - Waiting for payment.<br>';

			if($qrgenerator == "qrencode") {
			echo '<img src="functions/qrcode/qrencode.php?text='.$is_exists['invoice'].'">'; }

			echo $is_exists['invoice'].'<br>';
			echo $is_exists['price'] + $is_exists['fee'].' sat';

		}
		if($is_exists['status'] == 3) { echo 'Phase 4 / 4 - Repeat key verification.<br>'; }
		if($is_exists['status'] == 4) {

			echo 'Key ready to release!<br>';
			$game_id = $is_exists['id_game'];
			$db_game = $sqlite_games_db->querySingle("SELECT key FROM games WHERE (id = $game_id)", true);
			$decrypted_key = decrypt($db_game['key'], $hashed_db_password, $iv);
			echo '<a target="_blank" href="https://www.humblebundle.com/gift?key='.$decrypted_key.'">Link to game!</a>';
		}


} else {
	echo 'Unknown parameters';
}