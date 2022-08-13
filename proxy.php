<?php //é

ob_start();
ini_set( 'display_errors', 1 );

$cache = 3600;
$url = base64_decode( $_REQUEST['url'] );

$local = __DIR__.'/cache/'.md5( $url );

if( !is_file( $local ) || filemtime( $local ) < ( time() - $cache ) ) {
	$context = stream_context_create( array( 'http' => array(
		'header'=>"Connection: close\r\n".
			"User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0\n\r"
	) ) );
	$content = file_get_contents( $url, 0, $context );
	//$content = convertToUtf8( $content );
	//$content = file_get_contents( $url );
	die( $content );
	//$content = curl_file_get_contents( $url );
	$content = preg_replace( '#<content:encoded><!\[CDATA\[.*?\]\]></content:encoded>#s', '', $content );
	file_put_contents( $local, $content );
}

header( 'Content-Type: text/xml' );
//header( 'Content-Length: '.filesize( $local ) );
echo file_get_contents( $local );


function curl_file_get_contents($URL) {
	$c = curl_init();
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($c, CURLOPT_URL, $URL);
	$contents = curl_exec($c);
	curl_close($c);

	if ($contents) return $contents;
		else return FALSE;
}

function convertToUtf8( $text ) {
	if( preg_match( '#<\?xml.+encoding=("|\')?([^\1]*)\1.*\?>#i', $text, $matches ) ) {
		$charset = $matches[2];
		if( $charset && ( strtoupper( $charset )=='ISO-8859-1' || strtoupper( $charset )=='ISO-8859-15' ) ) {
			$text = utf8_encode( $text );
			$text = preg_replace('/<\?xml.+encoding=("|\')?([^\1]*)\1.*\?>/i', '<?xml version="1.0" encoding="UTF-8"?>', $text);
		}
	}
	return $text;
}