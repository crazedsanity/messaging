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
			MessageQueue::SESSIONKEY	=> array(
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
	
	
	public function test_getAll() {
		
		$_SESSION = array(
			MessageQueue::SESSIONKEY	=> array(
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
		
		$matchThis = array(
			0	=> array(
				'title'		=> "Fatal error",
				'body'		=> "there was a fatal error... somewhere",
				'url'		=> null,
				'linkText'	=> null,
				'type'		=> Message::TYPE_FATAL,
			),
			1	=> array(
				'title'		=> 'test',
				'body'		=> 'The message body',
				'url'		=> null,
				'linkText'	=> null,
				'type'		=> Message::DEFAULT_TYPE,
			),
			2	=> array(
				'title'		=> 'another',
				'body'		=> "a test body",
				'url'		=> 'http://localhost/?x=y',
				'linkText'	=> 'go to localhost',
				'type'		=> Message::DEFAULT_TYPE,
			),
		);
		
		$this->assertEquals(count($matchThis), $x->getCount());
		$this->assertEquals($matchThis, $x->getAll());
		$this->assertEquals(0, $x->getCount()); // ensure the queue gets cleared
	}
	
	
	public function test_save() {
		$test = array(
			Message::TYPE_FATAL	=> array(
				0	=> array(
					'title'	=> "Fatal error",
					'body'	=> "there was a fatal error... somewhere",
				)
			),
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
		);
		$_SESSION[MessageQueue::SESSIONKEY] = $test;
		
		$q = new MessageQueue(true);
		
		$q->save();
		
		// make sure Message::save() hasn't been called (which saves to "message" instead of "messages")
		$this->assertFalse(isset($_SESSION[Message::SESSIONKEY]));
		
		//fix up our test data so things are in the correct order.
		foreach($test as $k=>$data) {
			foreach($data as $i=>$msg) {
				$url = null;
				if(isset($msg['url'])) {
					$url = $msg['url'];
				}
				$txt = null;
				if(isset($msg['linkText'])) {
					$txt = $msg['linkText'];
				}
				$newMsg = array(
					'title'		=> $msg['title'],
					'body'		=> $msg['body'],
					'url'		=> $url,
					'type'		=> $k,
					'linkText'	=> $txt,
				);
				
				$test[$k][$i] = $newMsg;
			}
		}
		$this->assertEquals($test, $_SESSION[MessageQueue::SESSIONKEY], ToolBox::debug_print($_SESSION,0));
		$this->assertEquals(3, $q->getCount());
		$this->assertEquals(1, $q->getCount(Message::TYPE_FATAL));
		$this->assertEquals(2, $q->getCount(Message::TYPE_NOTICE));
		$this->assertEquals(0, $q->getCount(Message::TYPE_ERROR));
		$this->assertEquals(0, $q->getCount(Message::TYPE_STATUS));
	}
	
	
	public function test_clear() {
		$q = new MessageQueue();
		$this->assertEquals(0, $q->getCount());
		
		$test = new Message();
		$test->title = __METHOD__;
		$test->body = "another message here";
		
		$q->add($test);
		$this->assertEquals(1, $q->getCount());
		$q->clear();
		$this->assertEquals(0, $q->getCount());
		
		$n = new MessageQueue(true);
		$this->assertEquals(0, $n->getCount(), "the cleared queue did not actually save ...");
	}
}