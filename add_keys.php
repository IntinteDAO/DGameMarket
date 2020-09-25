<?php

include('config.php');
include('loader.php');
include('header.php');
include('template/navbar.php');
include('functions/check_redeem.php');
include('functions/id_regexp_check.php');
include('functions/encryption.php');

if(empty($_SESSION['login'])) {

	echo '<div class="col-12">You must be logged in to add game keys!</div>';
	include('footer.php');
	die();

}

include('template/add_keys.html'); // Include template

if(!empty($_POST['keys'])) { // If keys are added

$id_user = $_SESSION['id'];
$keys = $_POST['keys'];
$keys = explode("\n", htmlspecialchars($_POST['keys']));

	for($i=0; $i<=count($keys)-1; $i++) {
		$id = str_replace('https://www.humblebundle.com/gift?key=', '', $keys[$i]);
		$id = trim($id);

		if(id_verify($id)==true) {
			$check_redeem = is_redeemed($id);

			if($check_redeem['value'] == 0) {
				// Key works!
				$key = encrypt($id, $xor_cipher);
				$title = $check_redeem['title'];
				$db_user_exists = $sqlite_games_db->querySingle("INSERT INTO games (key, title, id_seller, id_buyer, status, price) VALUES ('$key', '$title', $id_user, 0, 0, 0)", true);
			} else if ($check_redeem['value'] == 1) {
				$key = encrypt($keys[$i], $xor_cipher);
				$db_user_exists = $sqlite_games_db->querySingle("INSERT INTO games (key, title, id_seller, id_buyer, status, price) VALUES ('$key', '$title', $id_user, 0, 997, 0)", true);
			} else {
				$key = encrypt($keys[$i], $xor_cipher);
				$db_user_exists = $sqlite_games_db->querySingle("INSERT INTO games (key, title, id_seller, id_buyer, status, price) VALUES ('$key', '$title', $id_user, 0, 998, 0)", true);
			}
		} else {
				$key = encrypt($keys[$i], $xor_cipher);
				$db_user_exists = $sqlite_games_db->querySingle("INSERT INTO games (key, title, id_seller, id_buyer, status, price) VALUES ('$key', '$title', $id_user, 0, 999, 0)", true);
		}
	}
echo '<meta http-equiv="refresh" content="0; url=profile.php?gameedit"/>';
}

include('footer.php');
