<?php

use crazedsanity\messaging\Message;
use crazedsanity\messaging\MessageQueue;
use crazedsanity\core\ToolBox;

class TestOfMessageQueue extends PHPUnit_Framework_TestCase {
	
	
	public function setUp() {
		$_SESSION = array();
		ToolBox::$debugPrintOpt = 1;
	}
	
	
	public function test_construct() {
		$x = new MessageQueue(false);
		$this->assertEquals(array(), $_SESSION);
		$this->assertEquals(0, $x->getCount());
		$this->assertEquals(0, $x->hasFatalError());
	}
	
	
	public function test_load() {
		$_SESSION = array(
			'message'	=> array(
				Message::TYPE_NOTICE => array(
					0	=> array(
						'title'	=> 'test',
						'body'	=> 'The message body',
					),
					1	=> array(
						'title'		=> 'another',
						'body'		=> "a test body",
						'url'		=> 'http://localhost/?x=y',
						'linkText'	=> 'go to localhost'
					)
				),
				Message::TYPE_FATAL	=> array(
					0	=> array(
						'title'	=> "Fatal error",
						'body'	=> "there was a fatal error... somewhere",
					)
				)
			)
		);
		$x = new MessageQueue(true);
		$this->assertEquals(3, $x->getCount());
		$this->assertEquals(1, $x->hasFatalError());
		$this->assertEquals(2, $x->getCount(Message::TYPE_NOTICE));
		$this->assertEquals(1, $x->getCount(Message::TYPE_FATAL));
		$this->assertEquals(0, $x->getCount(Message::TYPE_STATUS));
		$this->assertEquals(0, $x->getCount(Message::TYPE_ERROR));
	}
	
	
	
	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage invalid type
	 */
	public function test_getCount_invalidType() {
		$x = new MessageQueue();
		$x->getCount('invalid');
	}
}