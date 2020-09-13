<?php

function encrypt($plainText, $key) {
  $output = "";
  $keyPos = 0;
  $plainText = base64_encode($plainText);
  for ($p = 0; $p < strlen($plainText); $p++) {
    if ($keyPos > strlen($key) - 1) {
      $keyPos = 0;
    }
    $char = $plainText[$p] ^ $key[$keyPos];

    $bin = str_pad(decbin(ord($char)), 8, "0", STR_PAD_LEFT);
    $hex = dechex(bindec($bin));
    $hex = str_pad($hex, 2, "0", STR_PAD_LEFT);
    $output .= strtoupper($hex);
    $keyPos++;
  }
  return $output;
}

function decrypt($encryptedText, $key) {
  $output = "";
  $hex_arr = explode(" ", trim(chunk_split($encryptedText, 2, " ")));

  $keyPos = 0;
  for ($p = 0; $p < sizeof($hex_arr); $p++) {
    if ($keyPos > strlen($key) - 1) {
      $keyPos = 0;
    }
    $char = chr(hexdec($hex_arr[$p])) ^ $key[$keyPos];

    $output .= $char;
    $keyPos++;
  }
  return base64_decode($output);
}

?>