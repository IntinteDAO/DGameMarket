<?php

if(!empty($_GET['text'])) {

	if (preg_match("/^[a-z0-9]{250,300}$/", strtolower($_GET['text']))) {
		include('phpqrcode.php');
		QRcode::png(strtolower($_GET['text']));
	}

}