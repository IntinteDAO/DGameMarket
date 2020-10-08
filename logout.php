<?php

	header('X-Frame-Options: SAMEORIGIN');

	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
		$secure = true;
	} else {
		$secure = false;
	}

	$httponly = true;
	$samesite = 'strict';

	if(PHP_VERSION_ID < 70300) {
		session_set_cookie_params($maxlifetime, '/; samesite='.$samesite, $_SERVER['HTTP_HOST'], $secure, $httponly);
	} else {
		session_set_cookie_params([
			'lifetime' => $maxlifetime,
			'path' => '/',
			'domain' => $_SERVER['HTTP_HOST'],
			'secure' => $secure,
			'httponly' => $httponly,
			'samesite' => $samesite
		]);
	}

	session_start();
	session_destroy();
	echo '<meta http-equiv="refresh" content="0; url=index.php"/>';

?>