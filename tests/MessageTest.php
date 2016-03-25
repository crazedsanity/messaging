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
		
		$theMessage = new Message();
		$theMessage->setContents(array('title'=>'title', 'body'=>'the message'));
		
		$this->assertTrue(is_array($_SESSION), "Session was mangled after saving");
		$this->assertEquals(1, count($_SESSION), "Too much stuff in the session");
		$this->assertEquals($_SESSION[Message::SESSIONKEY], $theMessage->getContents(), "Items were not saved...?");
		$this->assertEquals('title', $theMessage->title);
		$this->assertEquals('the message', $theMessage->body);
		$this->assertEquals(Message::DEFAULT_TYPE, $theMessage->type);
		
		
		$theMessage->save();
		$this->assertEquals($_SESSION[Message::SESSIONKEY], $theMessage->getContents(), "Items were not saved...?");
	}
	
	
	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage invalid title
	 */
	public function test_invalidTitle() {
		$x = new Message();
		$x->title = '';
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage invalid body
	 */
	public function test_invalidBody() {
		$x = new Message();
		$x->title = 'a title';
		$x->body = 'x';
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage invalid title
	 */
	public function test_invalidTitle_after_validBody() {
		$x = new Message();
		$x->body = 'this is a valid body';
		$x->title = '';
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage invalid type
	 */
	public function test_invalidType() {
//		new Message('the title', 'here is a message', 'invalid');
		$x = new Message();
		$x->title = 'the title';
		$x->body = 'here is a message';
		$x->type = 'invalid';
	}
	
	
	public function test_getAndSet() {
		$x = new Message();
		$x->setContents(array(
			'title'	=> __CLASS__,
			'body'	=> __FUNCTION__,
		));
		
		$this->assertEquals(__CLASS__, $x->title);
		$this->assertEquals(__FUNCTION__, $x->body);
		$this->assertEquals(null, $x->url);
		$this->assertEquals(null, $x->linkText);
		
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
		$x = new Message();
		$x->setContents(array(
			'title'	=> __CLASS__,
			'body'	=> __FUNCTION__,
		));
		$x->invalid = __METHOD__;
	}
	
	
	public function test_getContents() {
		$x = new Message();
		$x->setContents(array(
			'title'	=> __CLASS__,
			'body'	=> __FUNCTION__,
		));
		
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