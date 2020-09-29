<?php

function crypto_price($crypto) {
	return round(json_decode(file('https://api.coincap.io/v2/assets/'.$crypto)[0], TRUE)['data']['priceUsd'], 3);
}