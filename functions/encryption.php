<?php

function encrypt($plainText, $key, $iv, $cipher = "aes-256-ctr") {

	$iv = base64_decode($iv);
	$ciphertext = openssl_encrypt($plainText, $cipher, $key, $options=0, $iv);
	return trim($ciphertext);

}

function decrypt($encryptedText, $key, $iv, $cipher = "aes-256-ctr") {

	$iv = base64_decode($iv);
	$original_plaintext = openssl_decrypt($encryptedText, $cipher, $key, $options=0, $iv);
	return trim($original_plaintext);

}

?>
