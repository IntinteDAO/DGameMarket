<?php
error_reporting(E_ALL);
include('../../config.php');

function get_json($url, $content=null, $method='POST') {

	global $access_token;
	print_r($url);
	print_r($content);

	$opts = array(
		'http'=>array(
			'method'=>$method,
			'header'=>'Content-Type: application/json'
		)
	);

		if($content !== null) {
			$opts['http']['content'] = $content;
		}

		if($access_token)$opts['http']['header'].="\r\n".'Authorization: Bearer '.$access_token;

		$context = stream_context_create($opts);
		$body=@file_get_contents($url,false,$context);
		print_r($http_response_header);

		if($body) {
			return json_decode($body,true);
		}

			return false;
}

$url='https://lndhub.herokuapp.com/';

function create_account() {

	global $url;
	$params=new stdClass();
	$params->partnerid='bluewallet';
	$params->accounttype='common';
	$json = get_json($url."create",json_encode($params));
	return $json;

}

	// Authorize
	$params = new stdClass();
	$params->login = $lndhub_user;
	$params->password = $lndhub_pass;
	$json = get_json($url."auth?type=auth",json_encode($params));
	$access_token = $json['access_token'];

function create_invoice($amount, $memo) {

	global $url;
	$params = new stdClass();
	$params->amt = $amount;
	$params->memo = $memo;
	$json = get_json($url."addinvoice",json_encode($params));
	return $json['payment_request'];

}


function get_all_invoices() {

	global $url;
	$params = new stdClass();
	$json = get_json($url."getuserinvoices", "", "GET");
	return $json;

}