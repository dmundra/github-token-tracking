<?php

require __DIR__ . '/vendor/autoload.php';

use Curl\Curl;

$curl = new Curl();
$curl->setHeader('Authorization', 'Bearer <GitHub Token>');
$curl->get('https://api.github.com/rate_limit');

$result = [];

if ($curl->error) {
	$result['message'] = 'Error: ' . $curl->errorMessage . "\n";
	$curl->diagnose();
} else {
	// echo 'Response:' . "\n<pre>";
	// var_dump($curl->response);
	// var_dump($curl->requestHeaders);
	// var_dump($curl->responseHeaders);
	$response = $curl->response;
	$result['limit'] = $response->rate->limit;
	$result['remaining'] = $response->rate->remaining;
	$result['used'] = $response->rate->used;
	date_default_timezone_set('America/Los_Angeles');
	$result['reset'] = date('l jS \of F Y h:i:s A', $response->rate->reset);
  $result['message'] = '';
}

header('Content-Type: application/json');
echo json_encode($result);
