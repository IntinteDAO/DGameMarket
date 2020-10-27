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
$count = count($keys)-1;
if(empty($keys[$i])) { die('No keys found!'); }

if($count > 5) {
	for($i=0; $i<=$count; $i++) {
		$id = substr(str_replace('https://www.humblebundle.com/gift?key=', '', $keys[$i]), 0, 50);
		$key = encrypt($id, $hashed_db_password, $iv);
		$title = base64_encode(trim('Waiting for verification'));
		pg_query("INSERT INTO games (key, title, id_seller, id_buyer, status, price) VALUES ('$key', '$title', $id_user, 0, 900, 0)");
	}
} else {

	for($i=0; $i<=$count; $i++) {

		$title = base64_encode('---');
		$id = str_replace('https://www.humblebundle.com/gift?key=', '', $keys[$i]);
		$id = trim($id);

		if(id_verify($id)==true) {
			$check_redeem = is_redeemed($id);
			$key = encrypt($id, $hashed_db_password, $iv);

			if($check_redeem['value'] == 0) {
				// Key works!
				$title = base64_encode(trim($check_redeem['title']));
				$status = 0;
			} else if ($check_redeem['value'] == 1) { $status = 997;
			} else { $status = 998; }

		} else { $status = 999; }

		pg_query("INSERT INTO games (key, title, id_seller, id_buyer, status, price) VALUES ('$key', '$title', $id_user, 0, $status, 0)");

	}
}

echo '<meta http-equiv="refresh" content="0; url=profile.php?gameedit"/>';
}
include('footer.php');
