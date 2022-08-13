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

abstract class DBSimpleObject extends DBObject {
	
	static $table = '';
	static $primary = '';
	public $lastUpdate = array();
	private $previous = array();
	
	public function __construct() {
		call_user_func_array( 'parent::__construct', func_get_args() );
		/*if( empty( $this::$table ) )
			throw new DBSimpleObjectException( 'Table not defined', 500 );
		if( empty( $this::$primary ) )
			throw new DBSimpleObjectException( 'Primary key not defined', 500 );
		return $this;*/
	}
	
	public function __set( $k, $v ) {
		if( is_string( $v ) )
			$v = trim( $v );
		
		if( preg_match('/^\[.*\]$/s', $v ) || preg_match('/^\{.*\}$/s', $v ) )
			if( is_array( $json = json_decode( $v, TRUE ) ) )
				$v = $json;

		parent::__set( $k == 'id' ? $this::$primary : $k, $v );
	}
	
	public function __get( $k ) {
		return parent::__get( $k == 'id' ? $this::$primary : $k );
	}
	
	public function get( $id ) {
		$this->_get( $id );
		return $this;
	}
	
	public function post( $data ) {
		$this->setData( $data );
		$this->_insert();
		return $this;
	}
	
	public function put( $id, $data ) {
		$this->_get( $id );
		$this->setData( $data );
		$this->_update();
		return $this;
	}
	
	public function delete( $id ) {
		$this->get( $id );
		$this->_delete();
		return $this;
	}
	
	protected function _get( $id ) {
		$sql = 'SELECT * FROM '.$this::$table.' WHERE '.$this::$primary.'=? LIMIT 1';
		$stmt = $this->pdo->prepare( $sql );
		$stmt->execute( array( $id ) );
		if( $stmt->rowCount() !== 1 )
			throw new DBSimpleObjectException( 'Not Found', 404 );
		
		$this->setData( $stmt->fetch( PDO::FETCH_ASSOC ) );
		$this->previous = $this->getData();
	}
	
	protected function _insert() {
		$this->id = '';
		$set = array();
		$data = $this->getData();
		foreach( $data as $k=>$v ) {
			$set[] = $k.'=:'.$k;
			if( !is_scalar( $v ) )
				$data[$k] = json_encode( $v );
		}
		$sql = 'INSERT INTO '.$this::$table.' SET '.implode( ',', $set );
		$stmt = $this->pdo->prepare( $sql );
		$stmt->execute( $data );
		$this->get( $this->pdo->lastInsertId() );
	}
	
	protected function _update() {
		$lastData = array_diff_assoc( $this->previous, $this->getData() );
		if( count( $lastData ) > 0 ) {
			$this->lastUpdate = $lastData;
			$data = array_diff_assoc( $this->getData(), $this->previous );
			$set = array();
			foreach( $data as $k=>$v ) {
				$set[] = $k.'=:'.$k;
				if( !is_scalar( $v ) )
					$data[$k] = json_encode( $v );
			}
			$data[$this::$primary] = $this->id;
			$sql = 'UPDATE '.$this::$table.' SET '.implode( ',', $set ).' WHERE '.$this::$primary.'=:'.$this::$primary;
			$stmt = $this->pdo->prepare( $sql );
			$stmt->execute( $data );
			$this->get( $this->id );
		}
	}
	
	protected function _delete() {
		$sql = 'DELETE FROM '.$this::$table.' WHERE '.$this::$primary.'=?';
		$stmt = $this->pdo->prepare( $sql );
		$stmt->execute( array( $this->id ) );
	}
	
}

class DBSimpleObjectException extends DBObjectException {}