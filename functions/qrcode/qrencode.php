<?php

if(!empty($_GET['text'])) {

	echo shell_exec('qrencode -o - '.$_GET['text']);
	header("Content-type: image/png");
}