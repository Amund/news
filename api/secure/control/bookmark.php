<?php

//noop::inspect();
$pdo = noop::pdo( 'db' );
$trails = explode( '/', noop::get( 'request/trail' ) );
$o = new Bookmark2( $pdo );

switch( noop::get( 'request/method' ) ) {

case 'GET':
	try {
		$o->get( $trails[0] );
	} catch( DBObjectException $e ) {
		noop::status( $e->getCode(), $e->getMessage() );
	}
	noop::output( $o, 'json' );

case 'POST':
	$obligatoires = array( 'titre','url' );
	$r = noop::filter( $_REQUEST, $obligatoires );
	if( count( $obligatoires ) !== count( $r ) )
		noop::status( 400, 'Bad Request' );
	
	try {
		$o->post( $r );
	} catch( DBObjectException $e ) {
		noop::status( $e->getCode(), $e->getMessage() );
	}
	noop::output( $o, 'json' );
	
case 'PUT':
	$obligatoires = array( 'titre','url' );
	$r = noop::filter( $_REQUEST, $obligatoires );
	if( count( $obligatoires ) !== count( $r ) )
		noop::status( 400, 'Bad Request' );
	
	try {
		$o->put( $trails[0], $r );
	} catch( DBObjectException $e ) {
		noop::status( $e->getCode(), $e->getMessage() );
	}
	noop::output( $o, 'json' );
	
case 'DELETE':
	try {
		$o->delete( $trails[0] );
	} catch( DBObjectException $e ) {
		noop::status( $e->getCode(), $e->getMessage() );
	}
	noop::output( $o, 'json' );
	
default:
	noop::status( 405, 'Method Not Allowed' );


}
