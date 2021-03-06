<?php

include('config.php');
include('loader.php');
include('header.php');
include('template/navbar.php');
include('functions/encryption.php');

if(isset($_SESSION['login'])) {
	echo '<meta http-equiv="refresh" content="0; url=index.php"/>';
}
else if(!empty($_POST['email']) && !empty($_POST['password'])) {

	$email = encrypt($_POST['email'], $hashed_db_password, $iv);
	$db_user_exists['password'] = '';
	$password = $_POST['password'];
	$db_user_exists = pg_fetch_array(pg_query("SELECT id, login, password, balance FROM users WHERE email='$email'"));

	if(!empty($db_user_exists)) {
		if (password_verify($password, $db_user_exists['password'])) {
			$_SESSION['login'] = decrypt($db_user_exists['login'], $hashed_db_password, $iv);
			$_SESSION['balance'] = $db_user_exists['balance'];
			$_SESSION['id'] = $db_user_exists['id'];
			echo '<meta http-equiv="refresh" content="0; url=index.php"/>';
		} else {
			echo '<div class="col-12"><b>Invalid password or login!<b></div>';
		}
	} else { echo '<div class="col-12"><b>Invalid password or login!<b></div>'; }
}

if(!isset($_SESSION['login'])) {
	include('template/login.html');
}

include('footer.php');

?>