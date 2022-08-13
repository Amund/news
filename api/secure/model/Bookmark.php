<?php

class Bookmark extends DBSimpleObject {
	
	static $table = 'bookmarks';
	static $primary = 'id_bookmark';
	
	private $data = array(
		'id_bookmark'=>NULL,
		'titre'=>NULL,
		'url'=>NULL,
	);
	
	public function __construct() {
		call_user_func_array( 'parent::__construct', func_get_args() );
		$this->setData( $this->data );
	}
	
	public function __set( $k, $v ) {
		switch( $k ) {
			case 'id_bookmark':
				if( !empty( $v ) && !self::isValidInt( $v ) )
					throw new BookmarkException( 'ID invalide', 500 );
				$v = (int) $v;
				parent::__set( $k, $v );
				break;
			default:
				parent::__set( $k, $v );
		}
	}
	
}

class BookmarkException extends DBSimpleObjectException {}