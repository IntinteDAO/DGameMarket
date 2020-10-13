<?php

$website_title = "Intinte DGameMarket";
$db_password = "CHANGE ME!";
$sqlite_database_file = "database.sqlite3";
$iv = ""; // AES 256 IV required and stored by base64 [ base64_encode(openssl_random_pseudo_bytes(16)); ]
$fee = 5; // Value in cents
$expire_buy_time = 1800;

$payment_provider = "lndhub";
$lndhub_user = "USERNAME";
$lndhub_pass = "PASSWORD";