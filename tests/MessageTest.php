<?php

use crazedsanity\messaging\Message;
use crazedsanity\core\ToolBox;

class TestOfMessage extends PHPUnit_Framework_TestCase {
	
	
	public function setUp() {
		$_SESSION = array();
	}
	
	
	public function test_save() {
		$_SESSION = array();
		
		$this->assertTrue(is_array($_SESSION), "Session isn't an array...?");
		$this->assertEquals(0, count($_SESSION), "Session is already populated...?");
		
		$theMessage = new Message('title', 'the message');
		
		$this->assertTrue(is_array($_SESSION), "Session was mangled after saving");
		$this->assertEquals(1, count($_SESSION), "Too much stuff in the session");
		$this->assertEquals($_SESSION[Message::SESSIONKEY], $theMessage->getContents());
		
		
	}
	
	
	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage invalid title
	 */
	public function test_invalidTitle() {
		new Message('', '');
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage invalid message
	 */
	public function test_invalidMessage() {
		new Message('the title', '');
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage invalid type
	 */
	public function test_invalidType() {
		new Message('the title', 'here is a message', 'invalid');
	}
	
	
	public function test_getAndSet() {
		$x = new Message(__CLASS__, __FUNCTION__);
		
		$this->assertEquals(__CLASS__, $x->title);
		$this->assertEquals(__FUNCTION__, $x->body);
		$this->assertEquals('', $x->url);
		$this->assertEquals('', $x->linkText);
		
		$x->title = "TEST";
		$this->assertEquals("TEST", $x->title);
		
		$x->body = "message";
		$this->assertEquals("message", $x->body);
		
		$x->url = 'http://localhost';
		$this->assertEquals('http://localhost', $x->url);
		
		$x->linkText = 'stuff';
		$this->assertEquals('stuff', $x->linkText);
	}
	
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function test_invalidSet() {
		$x = new Message(__CLASS__, __FUNCTION__);
		$x->invalid = __METHOD__;
	}
	
	
	public function test_getContents() {
		$x = new Message(__CLASS__, __FUNCTION__);
		
		$message = array(
			'title'		=> __CLASS__,
			'body'		=> __FUNCTION__,
			'url'		=> '',
			'type'		=> Message::DEFAULT_TYPE,
			'linkText'	=> '',
		);
		
		$this->assertEquals($message, $x->getContents());
		$this->assertEquals($message, $x->contents);
	}
}