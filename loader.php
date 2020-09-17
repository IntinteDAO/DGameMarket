<?php

$xor_cipher = hash('sha512', $xor_password);

// Init database
if(file_exists($sqlite_database_file)) {
    $sqlite_db = new SQLite3($sqlite_database_file);
}

$sqlite_users_db = $sqlite_db;


?>