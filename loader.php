<?php

$hashed_db_password = hash('sha512', $db_password);

// Init database
if(file_exists($sqlite_database_file)) {
    $sqlite_db = new SQLite3($sqlite_database_file);
}

// Secure Session
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

$sqlite_users_db = $sqlite_db;
$sqlite_games_db = $sqlite_db;
$sqlite_invoices_db = $sqlite_db;
$sqlite_withdraws_db = $sqlite_db;

?>