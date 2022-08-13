<?php

ini_set( 'display_errors', 1 );
require_once __DIR__.'/../noop.php';

class NoopExceptionTest extends PHPUnit_Framework_TestCase {
	
	function tearDown() {
		noop::set( 'config/path/controller', 'secure/control/' );
		noop::set( 'config/default/controller', 'index' );
	}
	
    /**
     * @expectedException NoopConfigException
     */
	function testNoopControllerNotExistingPath() {
		noop::set( 'config/path/controller', __DIR__.'/non-existing-path' );
		noop::set( 'config/default/controller', 'existing-controller' );
		try {
			noop::_controller( '' );
		} catch( InvalidArgumentException $e ) {
			return;
		}
		$this->fail();
	}
	
    /**
     * @expectedException NoopConfigException
     */
	function testNoopControllerEmptyDefault() {
		noop::set( 'config/path/controller', __DIR__ );
		noop::set( 'config/default/controller', '' );
		try {
			noop::_controller( '' );
		} catch( InvalidArgumentException $expected ) {
			return;
		}
		$this->fail();
	}
	
    /**
     * @expectedException NoopControllerException
     */
	function testNoopControllerNotExists() {
		noop::set( 'config/path/controller', __DIR__ );
		noop::set( 'config/default/controller', 'default' );
		try {
			noop::_controller( 'sub-without-default' );
		} catch( InvalidArgumentException $expected ) {
			return;
		}
		$this->fail();
	}
	
}
