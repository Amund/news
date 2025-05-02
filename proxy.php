<?php

ini_set( 'display_errors', 1 );

// load rss content from url
$url = base64_decode( $_REQUEST['url'] );
$content = curl_file_get_contents( $url );

// filter content
$dom = new DOMDocument();
$dom->recover = TRUE;
$dom->loadXML($content);
$content = $dom->saveXML($dom->documentElement);

// output content
ob_start();
header( 'Content-Type: text/xml; charset=utf-8' );
die($content);

function curl_file_get_contents($url) {
	$headers = [
		'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:137.0) Gecko/20100101 Firefox/137.0',
		'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
		'Accept-Language: fr,fr-FR;q=0.8,en-US;q=0.5,en;q=0.3',
		'Connection: keep-alive',
		'Pragma: no-cache',
		'Cache-Control: no-cache',
	];

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}
