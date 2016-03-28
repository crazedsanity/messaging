<?php

namespace crazedsanity\messaging;

use crazedsanity\messaging\Message;
use crazedsanity\core\ToolBox;

class MessageQueue extends Message {
    
    private $_messages = array();
    const SESSIONKEY = "messages";
    
	//----------------------------------------------------------------------------
    public function __construct($load=false) {
		parent::__construct();
        if($load===true) {
            $this->load();
        }
        else {
            $this->init();
        }
    }
	//----------------------------------------------------------------------------
    
    
    
	//----------------------------------------------------------------------------
    protected function init() {
        foreach(Message::$validTypes as $k) {
            if(!isset($this->_messages[$k]) || !is_array($this->_messages[$k])) {
                $this->_messages[$k] = array();
            }
        }
    }
	//----------------------------------------------------------------------------
    
    
    
	//----------------------------------------------------------------------------
    public function hasFatalError() {
        return count($this->_messages[Message::TYPE_FATAL]);
    }
	//----------------------------------------------------------------------------
    
    
    
	//----------------------------------------------------------------------------
    public function load() {
        if(isset($_SESSION[self::SESSIONKEY]) && is_array($_SESSION[self::SESSIONKEY])) {
            foreach($_SESSION[self::SESSIONKEY] as $type=>$list) {
                foreach($list as $num=>$msgData) {
					$x = new Message(false);
					$x->type = $type;
					$x->setContents($msgData);
					$this->add($x);
                }
            }
        }
        $this->init();
    }
	//----------------------------------------------------------------------------
    
    
    
	//----------------------------------------------------------------------------
    public function save() {
//        $_SESSION[self::SESSIONKEY] = $this->_messages;
		foreach($this->_messages as $type=>$list) {
			foreach($list as $k=>$obj) {
				$_SESSION[self::SESSIONKEY][$type][$k] = $obj->getContents();
			}
		}
    }
	//----------------------------------------------------------------------------
    
    
    
	//----------------------------------------------------------------------------
    public function __destruct() {
        $this->save();
    }
	//----------------------------------------------------------------------------

	
	
	//----------------------------------------------------------------------------
	public function add(Message $msg) {
	    $this->_messages[$msg->type][] = $msg;
		$this->save();
	}
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	public function getCount($byType=null) {
		$retval = 0;
		if(is_null($byType)) {
			foreach($this->_messages as $data) {
				$retval += count($data);
			}
		}
		elseif(!is_null($byType) && in_array($byType, Message::$validTypes)) {
			$retval = count($this->_messages[$byType]);
		}
		else {
			throw new \InvalidArgumentException("invalid type (". $byType .")");
		}
		return $retval;
	}
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	public function getAll() {
		$theList = array();
		foreach(Message::$typePrecedence as $type) {
			foreach($this->_messages[$type] as $msg) {
				$theList[] = $msg->getContents();
			}
		}
		return $theList;
	}
	//----------------------------------------------------------------------------
}