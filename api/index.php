<?php

//setlocale( LC_ALL, array( 'fr_FR.UTF-8', 'fr_FR', 'fr' ) );
//ini_set( 'date.timezone', 'Europe/Paris' );

require 'noop.php';

noop::config( array(
	'path'=>array(
		'model'=>'secure/model',
	),
	'pdo'=>array(
		'db'=>'mysql,host=localhost;dbname=avnl,root,super08081974'
	),
) );

noop::start();


// Functions

function __autoload( $c ) {
    $f = noop::get( 'config/path/model' ).DIRECTORY_SEPARATOR.$c.'.php';;
    if( !is_file( $f ) )
        throw new Exception( 'Classe "'.$c.'" introuvable' );
    require_once $f;
}
