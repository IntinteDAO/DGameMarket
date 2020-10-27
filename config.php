<?php

$website_title = "Intinte DGameMarket";
$db_password = "CHANGE ME!";
$iv = ""; // AES 256 IV required and stored by base64 [ base64_encode(openssl_random_pseudo_bytes(16)); ]
$fee = 5; // Value in cents
$expire_buy_time = 1800;
$limit_games_per_page = 8;

// Database config
$hostname = "localhost";
$port = 5432;
$dbname = "dbname";
$dbuser = "dbuser";
$dbpassword = "dbpass";

$payment_provider = "lndhub";
$lndhub_user = "USERNAME";
$lndhub_pass = "PASSWORD";