<?php

class DBTriplet {
	
	protected $pdo;
	private $data = array( 'class'=>'' );
	private $dataErr = array();
	private $previous = array();
	public $lastUpdate = array();
	
	public function __construct( PDO $pdo ) {
		$this->pdo = $pdo;
		$this->class = __CLASS__;
	}
	
	public function __set( $k, $v ) {
		$this->data[$k] = $v;
	}
	
	public function __get( $k ) {
		if( isset( $this->data[$k] ) || array_key_exists( $k, $this->data )  )
			return $this->data[$k];
		throw new DBObjectException( 'Unknown object property ('.$k.')' );
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
		$sql = 'SELECT * FROM triplets WHERE id=?';
		$stmt = $this->pdo->prepare( $sql );
		$stmt->execute( array( $id ) );
		if( $stmt->rowCount() < 0 )
			throw new DBSimpleObjectException( 'Not Found', 404 );
		
		$data = array();
		$rows = $stmt->fetchAll( PDO::FETCH_ASSOC );
		foreach( $rows as $row )
			$data[$row['key']] = $row['value'];
		
		$this->setData( array_intersect_key( $data, $this->getData() ) );
		$this->previous = $this->getData();
	}
	
	protected function _insert() {
		$this->id = '';
		$set = array();
		$data = $this->getData();
		
		$sql = '
			INSERT INTO triplets
			SET
				id=( SELECT (MAX(id)+1) FROM triplets ),
				key=?,
				value=?
		';
		$stmt = $this->pdo->prepare( $sql );
		
		foreach( $data as $k=>$v ) {
			if( !is_scalar( $v ) )
				$data[$k] = json_encode( $v );
			$stmt->execute( array( $k, $v ) );
		}
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

class DBTripletException extends NoopException {}