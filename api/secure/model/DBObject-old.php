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

class DBObjectException extends Exception {}

abstract class DBObject {
	
	static $table = '';
	static $primary = '';
	private $pdo;
	private $data    = array();
	private $dataErr = array();

	public function __construct( PDO $pdo ) {
		$this->pdo = $pdo;
		if( empty( $this::$table ) )
			$this->err( 'TABLE_NOT_DEFINED' );
		if( empty( $this::$primary ) )
			$this->err( 'PRIMARY_NOT_DEFINED' );
	}
	
	public function __set( $k, $v ) {
		$this->data[$k] = $v;
	}
	
	public function __get( $k ) {
		if( !isset( $this->data[$k] ) )
			$this->err( 'PROPERTY_NOT_FOUND' );
		return $this->data[$k];
	}
	
	public function setData( array $data ) {
		$this->dataErr = array();
		foreach( $data as $k=>$v ) {
			try {
				$this->$k = $v;
			} catch( DBObjectException $e ) {
				$this->dataErr[$k] = $e->getMessage();
			}
		}
		if( count( $this->dataErr ) > 0 )
			$this->err( 'DATA_ERRORS' );
	}
	
	public function getData() {
		return $this->data;
	}
	
	public function getDataErr() {
		return $this->dataErr;
	}
	
	public function get( $primary_value ) {
		$sql = 'SELECT * FROM '.$this::$table.' WHERE '.$this::$primary.'=? LIMIT 1';
		$stmt = $this->pdo->prepare( $sql );
		$stmt->execute( array( $primary_value ) );
		if( $stmt->rowCount() !== 1 ) return FALSE;
		$this->setData( $stmt->fetch( PDO::FETCH_ASSOC ) );
		return TRUE;
	}
	
	public function put( $primary_value='' ) {
		if( !isset( $this->data[$this::$primary] ) )
			$this->err( 'PRIMARY_NOT_FOUND' );
		$primary_value = ( empty( $primary_value ) ? $this->data[$this::$primary] : $primary_value );
		$keys = array_keys( $this->data );
		$values = array_values( $this->data );
		foreach( $values as $k=>$v ) if( !is_scalar( $v ) ) $values[$k] = json_encode( $v );
		if( $this->exists( $primary_value ) ) {
			// UPDATE
			$sql_pairs = array();
			foreach( $this->data as $k=>$v ) $sql_pairs[] = $k.'=?';
			$sql_pairs = implode( ',', $sql_pairs );
			$sql = 'UPDATE '.$this::$table.' SET '.$sql_pairs.' WHERE '.$this::$primary.'="'.$primary_value.'" LIMIT 1';
			$stmt = $this->pdo->prepare( $sql );
			$stmt->execute( $values );
		} else {
			// INSERT
			$sql_keys = implode( ',', $keys );
			$sql_values = implode( ',', array_fill( 0, count( $keys ), '?' ) );
			$sql = 'INSERT INTO '.$this::$table.' ('.$sql_keys.') VALUES ('.$sql_values.')';
			$stmt = $this->pdo->prepare( $sql );
			$stmt->execute( $values );
			$primary_value = $this->pdo->lastInsertId();
		}
		return $this->get( $primary_value );
	}
	
	public function del( $primary_value='' ) {
		$primary_value = ( empty( $primary_value ) ? $this->data[$this::$primary] : $primary_value );
		if( $this->exists( $primary_value ) ) {
			$sql = 'DELETE FROM '.$this::$table.' WHERE '.$this::$primary.'="'.$primary_value.'" LIMIT 1';
			$stmt = $this->pdo->query( $sql );
		} else {
			return FALSE;
		}
		return TRUE;
	}
	
	public function exists( $primary_value ) {
		if( empty( $primary_value ) ) return FALSE;
		$sql = 'SELECT '.$this::$primary.' FROM '.$this::$table.' WHERE '.$this::$primary.'=? LIMIT 1';
		$stmt = $this->pdo->prepare( $sql );
		$stmt->execute( array( $primary_value ) );
		return ( $stmt->rowCount() === 1 );
	}
	
	public function err( $message='', $code=0, $previous=NULL ) {
		$e = new DBObjectException(
			strtoupper( get_class( $this ) ).'_'.$message,
			$code,
			$previous
		);
		throw $e;
	}
	
	public static function isValidDateTime( $v ) {
		return preg_match( '#^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$#', $v );
	}
	
}
