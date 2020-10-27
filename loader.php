<?php

$hashed_db_password = hash('sha512', $db_password);

// Init database
$postgresql_db = pg_connect("host=$hostname port=$port dbname=$dbname user=$dbuser password=$dbpassword");

// Secure Session
    header('X-Frame-Options: SAMEORIGIN');


    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
	$secure = true;
    } else {
	$secure = false;
    }

    $httponly = true;
    $samesite = 'strict';
    $maxlifetime = 60 * 60 * 24;

if(php_sapi_name()!="cli") {
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
}

    session_start();

?>