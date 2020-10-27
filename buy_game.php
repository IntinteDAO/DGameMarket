<?php

include("config.php");
include("loader.php");
include("header.php");
echo '<div class="container"><div class="row">';
include("functions/oracle.php");
include("functions/encryption.php");

if(!isset($_SESSION['login'])) {
	echo '<div class="col-12"><b>It is recommended to log in with your account!</b></div>';
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
		$db_game_data = pg_fetch_array(pg_query("SELECT price, key FROM games WHERE (id = $game_id AND status=1)"));

		if(!empty($db_game_data)) {

			$bitcoin_price = crypto_price('bitcoin');
			$random_id = trim(uniqid(md5(microtime().$hashed_db_password)));
			$price = floor(((($db_game_data['price']) / 100000000 / 100) * $bitcoin_price) * 100000000);
			$fee = floor(((($fee) / 100000000 / 100) * $bitcoin_price) * 100000000);

			if($buyer != 0) {
				$is_already_added = pg_fetch_array(pg_query("SELECT id_uniq FROM invoices WHERE (id_game = $game_id AND buyer = $buyer AND timestamp < $check_expired_time)"))['id_uniq'];
			}

			if(empty($is_already_added)) {
				pg_query("INSERT INTO invoices (id_game, id_uniq, buyer, price, fee, invoice, timestamp, status) VALUES ($game_id, '$random_id', $buyer, $price, $fee, '0', $timestamp, 0)");
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

	$is_exists = pg_fetch_array(pg_query("SELECT id_game, buyer, timestamp, id_uniq, status, invoice, price, fee FROM invoices WHERE (id_uniq = '$id')"));

		if(empty($is_exists)) { die("Sorry, this invoice ID is invalid!"); }
		if($is_exists['buyer'] != $buyer ) { die("Sorry, this invoice is not for you!"); }
		if((($is_exists['timestamp'] + $expire_buy_time) < $timestamp ) && $is_exists['status']<3) { die("Sorry, this invoice is expired!"); }
		if($is_exists['status']<3) { echo '<div class="col-12">Time to expire: '.($is_exists['timestamp'] - $timestamp + $expire_buy_time).' seconds</div>'; }
		if($is_exists['status'] == 0) { echo '<div class="col-12">Phase 1 / 3 - Verification of the game key.</div><meta http-equiv="refresh" content="10">'; }
		if($is_exists['status'] == 1) {

			echo '<div class="col-12">Phase 2 / 3 - Waiting for payment.</div><meta http-equiv="refresh" content="10">';

			echo '<div class="col-12"><img src="functions/qrcode/qrcode.php?text='.$is_exists['invoice'].'"></div>';

			echo '<div class="col-12"><p class="text-break">'.$is_exists['invoice'].'</p></div>';
			echo '<div class="col-12"><b>'.($is_exists['price'] + $is_exists['fee']).'</b> sat</div>';

		}
		if($is_exists['status'] == 2) { echo '<div class="col-12">Phase 3 / 3 - Repeat key verification.</div><meta http-equiv="refresh" content="10">'; }
		if($is_exists['status'] == 3) {

			echo '<div class="col-12">Key ready to release!<br>';
			$game_id = $is_exists['id_game'];
			$db_game = pg_fetch_array(pg_query("SELECT key FROM games WHERE (id = $game_id)"));
			$decrypted_key = decrypt($db_game['key'], $hashed_db_password, $iv);
			echo '<a target="_blank" href="https://www.humblebundle.com/gift?key='.$decrypted_key.'">Link to game!</a></div>';
		}


} else {
	echo 'Unknown parameters';
}

include("footer.php");