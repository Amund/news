<?php

// MIT License
// Copyright (c) 2011 Dimitri Avenel

// Permission is hereby granted, free of charge, to any person obtaining
// a copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to
// permit persons to whom the Software is furnished to do so, subject to
// the following conditions:

// The above copyright notice and this permission notice shall be
// included in all copies or substantial portions of the Software.

// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
// EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
// NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
// LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
// OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
// WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

abstract class DBObject {
	
	protected $pdo;
	private $sql     = array();
	private $data    = array();
	private $dataErr = array();

	public function __construct( PDO $pdo ) {
		$this->pdo = $pdo;
	}
	
	public function __set( $k, $v ) {
		$this->data[$k] = $v;
	}
	
	public function __get( $k ) {
		if( isset( $this->data[$k] ) || array_key_exists( $k, $this->data )  )
			return $this->data[$k];
		throw new DBObjectException( 'Unknown object property "'.$k.'"' );
	}
	
	public function __toString() {
		return json_encode( $this->getData() );
	}
	
	public function setData( array $data ) {
		$this->dataErr = array();
		$previous = NULL;
		foreach( $data as $k=>$v ) {
			try {
				$this->$k = $v;
			} catch( DBObjectException $e ) {
				$this->dataErr[$k] = $e->getMessage();
				if( $previous == NULL )
					$previous = $e;
			}
		}
		if( count( $this->dataErr ) > 0 )
			throw new DBObjectException( 'Cet objet comporte des erreurs', 200, $previous );
		return $this;
	}
	
	public function getData() {
		return $this->data;
	}
	
	public function getDataErr() {
		return $this->dataErr;
	}
	
	public function getFields() {
		return array_keys($this->data);
	}

	public function get( $id ) {}
	public function post( $data ) {}
	public function put( $id, $data ) {}
	public function delete( $id ) {}
	protected function _get( $id ) {}
	protected function _insert() {}
	protected function _update() {}
	protected function _delete() {}
	
	public static function isValidDate( $v ) {
		return (bool) preg_match( '#^\d{4}-\d{2}-\d{2}$#', $v );
	}
	
	public static function isValidTime( $v ) {
		return (bool) preg_match( '#^\d{2}:\d{2}:\d{2}$#', $v );
	}
	
	public static function isValidDateTime( $v ) {
		return (bool) preg_match( '#^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$#', $v );
	}
	
	public static function isValidInt( $v ) {
		return is_numeric( $v ) && ( $v == (int) $v );
	}
	
	public static function isValidBool( $v ) {
		return ( $v == 0 || $v == 1 );
	}
	
}

class DBObjectException extends NoopException {}