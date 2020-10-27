<?php
include('config.php');
include('loader.php');
include('header.php');
include('template/navbar.php');
include('functions/encryption.php');

if(!empty($_POST['login']) && !empty($_POST['email']) && !empty($_POST['password1']) && !empty($_POST['password2'])) {
	$error = 0;

	if($_POST['password1']!=$_POST['password2']) { echo '<div class="col-12"><b>The passwords are not the same</b></div>'; $error = 1; }

	if (preg_match("@^([a-z]){3,32}$@", $_POST['login'])) {
		$login = encrypt($_POST['login'], $hashed_db_password, $iv);
		$db_is_login_exists = pg_fetch_array(pg_query("SELECT id FROM users WHERE login='$login'"));
	} else {
		$login = 1;
		echo '<div class="col-12"><b>Your nickname does not meet our criteria - minimum 3 letters, maximum 32; only small letters.</b></div>'; $error=1;
	}

	if (preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $_POST['email'])) {
		$email = encrypt($_POST['email'], $hashed_db_password, $iv);
		$db_is_email_exists = pg_fetch_array(pg_query("SELECT email FROM users WHERE email='$email'"));
	} else {
		$email = 1;
		echo '<div class="col-12"><b>Your email is not correct.</b></div>'; $error=1;
	}

	if(!empty($db_is_login_exists)) { echo '<div class="col-12"><b>The user with this login is already in our database</b></div>'; $error=1; }
	if(!empty($db_is_email_exists)) { echo '<div class="col-12"><b>The user with this email is already in our database</b></div>'; $error=1; }

	if($error==0) {
		$hash = password_hash($_POST['password1'], PASSWORD_BCRYPT, ['cost' => 13]);
		pg_query("INSERT INTO users (login, email, password, balance) VALUES ('$login', '$email', '$hash', 0)");
		$_SESSION['login'] = $_POST['login'];
		$_SESSION['balance'] = 0;
		$id = pg_fetch_array(pg_query("SELECT id FROM users WHERE email='$email'"))[0];
		$_SESSION['id'] = $id['id'];
		echo '<meta http-equiv="refresh" content="0; url=index.php"/>';
	} else {
		include('template/register.html');
	}

} else {
include('template/register.html');
}

include('footer.php');
?>