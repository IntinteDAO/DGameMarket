<?php

include('config.php');
include('loader.php');
include('header.php');
include('template/navbar.php');

if(empty($_SESSION['login'])) {
	echo '<div class="col-12">You must be logged in to withdraw funds from your account</div>';

} else if(!empty($_POST['lninvoice']) && empty($_POST['confirm'])) {

	$lninvoice = trim(strtolower($_POST['lninvoice']));

	if (preg_match("/^[a-z0-9]{250,400}$/", $lninvoice)) {
		include('functions/payment_providers/'.$payment_provider.'.php');
		$invoice_info = decode_invoice($lninvoice);

		if($invoice_info['timestamp'] + $invoice_info['expiry'] < time()) { $error['expired'] = 1; }
		$id_user = $_SESSION['id'];
		$balance = pg_fetch_array(pg_query("SELECT balance FROM users WHERE id = $id_user"))['balance'];
		if($balance < $invoice_info['num_satoshis']) { $error['balance'] = 1; }
		if($invoice_info['num_satoshis'] == 0) { $error['balance'] = 2; }

		if(empty($error['balance']) && empty($error['expired'])) {
			include('template/withdraw_confirm.php');
			$id_user = $_SESSION['id'];
			$time = time();
			$sats = $invoice_info['num_satoshis'];
			pg_query("INSERT INTO withdraws (id_user, lninvoice, timestamp, sats, status) VALUES ($id_user, '$lninvoice', $time, $sats, '0')"); // Insert invoice to database with status 0 (disabled)
			echo withdraw_confirmation($invoice_info['description'], $invoice_info['num_satoshis'], $lninvoice);
		} else {
			if($error['balance'] == 1) { echo '<div class="col-12">You want to pay out more than you have!</div>'; }
			if($error['balance'] == 2) { echo '<div class="col-12">Your invoice requires "any amount" that DGameMarket does not support</div>'; }
			if(!empty($error['expired'])) { echo '<div class="col-12">This invoice is expired!</div>'; }
		}
	}


} else if(!empty($_POST['lninvoice']) && !empty($_POST['confirm'])) {

			if (preg_match("/^[a-z0-9]{250,400}$/", strtolower($_POST['lninvoice']))) {
				$lninvoice = trim(strtolower($_POST['lninvoice']));
				$id_user = $_SESSION['id'];
				$is_already_confirmed = pg_fetch_array(pg_query("SELECT status FROM withdraws WHERE (lninvoice = '$lninvoice' AND id_user = $id_user)"));

				if(empty($is_already_confirmed[0])) {
					pg_query("UPDATE withdraws SET status='1' WHERE (lninvoice = '$lninvoice' AND id_user = $id_user)"); // Set status 1 (confirmed withdraw)
					echo '<div class="col-12">The withdrawal of funds was added to the payout system. In case of problems, contact the node administrators.</div>';
				} else if ($is_already_confirmed[0] == "1") {
					echo '<div class="col-12">This invoice is in progress, please wait (if it takes more than 5 minutes, contact the node administrator).</div>';
				} else if ($is_already_confirmed[0] == "OK"){
					echo '<div class="col-12">This invoice was executed correctly.</div>';
				} else {
					echo '<div class="col-12">This invoice was NOT executed correctly, check your user profile for more information.</div>';
				}
			}

} else {
	$id_user = $_SESSION['id'];
	$balance = pg_fetch_array(pg_query("SELECT balance FROM users WHERE id = $id_user"))['balance'];

	if($balance > 0) {
		include('template/withdraw.html');
	} else {
		echo '<div class="col-12">You do not have enough funds to withdraw.</div>';
	}


}

include('footer.php');