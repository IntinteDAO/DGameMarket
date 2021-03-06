<?php
//include('../../config.php');
error_reporting(E_ALL);

function get_json($url, $content=null, $method='POST') {

	global $access_token;
//	print_r($url);
//	print_r($content);

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
//		print_r($http_response_header);

		if($body) {
			return json_decode($body,true);
		}

			return false;
}

$url='https://lndhub.herokuapp.com/';

if(empty($lndhub_user) || empty($lndhub_pass)) { die('Misconfiguration detected'); }

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

function decode_invoice($invoice) {

	global $url;
	$params = new stdClass();
	$params->limit = $invoice;
	$json = get_json($url."decodeinvoice?invoice=$invoice", "", "GET");
	return $json;

}

function getinfo() {

	global $url;
	$params = new stdClass();
	$json = get_json($url."getinfo", "", "GET");
	return $json;

}

function check_route($pubkey, $amt) {

	global $url;
	$params = new stdClass();
	$params->destination = $pubkey;
	$params->amt = $amt;
	$json = get_json($url."checkroute?{destination=$pubkey&amt=1}", "", "GET");
	return $json;

}

function payinvoice($invoice) {

	global $url;
	$params = new stdClass();
	$params->invoice = $invoice;
	$json = get_json($url."payinvoice",json_encode($params));
	return $json;

}