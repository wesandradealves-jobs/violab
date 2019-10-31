<?php 
	include_once 'config/PagSeguroConfig.php';

	$arrayData = [
		'email' => $_POST['email'],
		'token' => $_POST['token']
	];

	$data = http_build_query($arrayData);

	$curl=curl_init( $urls['session'] );
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	$xml = curl_exec($curl);

	$response = simplexml_load_string($xml);

	$json = json_encode($response);
	// $arr = json_decode($json,true);

	print_r($json);
 ?>