<?php

class Bookmark2 extends DBTriplet {

	private $data = array(
		'titre'=>NULL,
		'url'=>NULL,
	);
	
	public function __construct() {
		call_user_func_array( 'parent::__construct', func_get_args() );
		$this->class = __CLASS__;
		$this->setData( $this->data );
	}
	
}

class BookmarkException extends DBTripletException {}