<?php

namespace crazedsanity\messaging;

use \InvalidArgumentException;

class Message {
    
    const TYPE_NOTICE = "notice";
    const TYPE_STATUS = "status";
    const TYPE_ERROR  = "error";
    const TYPE_FATAL  = "fatal";
	
	CONST SESSIONKEY = '/message';
    
	const DEFAULT_TYPE = self::TYPE_NOTICE;
    
    // this is a sort of hack to make it easier to know the list of valid types.
    public static $validTypes = array(
        self::TYPE_NOTICE,
        self::TYPE_STATUS,
        self::TYPE_ERROR,
        self::TYPE_FATAL
    );
	
	// set in order of importance (most -> least)
	public static $typePrecedence = array(
		self::TYPE_FATAL,
		self::TYPE_ERROR,
		self::TYPE_STATUS,
		self::TYPE_NOTICE,
	);
    
    
    private $title;
    private $body;
	private $type;
    private $url;
    private $linkText;


	//----------------------------------------------------------------------------
	/**
	 * 
	 * @param type $title
	 * @param type $message
	 * @param type $type
	 * @param type $linkUrl
	 * @param type $linkText
	 * @throws InvalidArgumentException
	 */
    public function __construct($title, $message, $type=self::DEFAULT_TYPE, $linkUrl=null, $linkText=null) {
        if(!is_null($title) && strlen($title) >2) {
            $this->title = $title;
        }
        else {
            throw new InvalidArgumentException("invalid title");
        }
        
        if(!is_null($message) && strlen($message) > 5) {
            $this->body = $message;
        }
        else {
            throw new InvalidArgumentException("invalid message length");
        }
        
        if(!is_null($type) && in_array($type, self::$validTypes)) {
            $this->type = $type;
        }
        else {
            throw new InvalidArgumentException("invalid type");
        }
        
        if(!is_null($linkUrl) && strlen($linkUrl) > 0 && !is_null($linkText) && strlen($linkText) > 0) {
            $this->url = $linkUrl;
            $this->linkText = $linkText;
        }
		$this->save();
    }
	//----------------------------------------------------------------------------



	//----------------------------------------------------------------------------
    public function getContents() {
        $retval = array(
            'title'		=> $this->title,
            'body'		=> $this->body,
            'url'		=> $this->url,
			'type'		=> $this->type,
            'linkText'	=> $this->linkText,
        );
        return $retval;
    }
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	public function setContents(array $msg) {
		foreach($msg as $field => $value) {
			$this->$field = $value;
		}
		$this->save();
	}
	//----------------------------------------------------------------------------



	//----------------------------------------------------------------------------
    public function __get($name) {
		if($name == 'contents') {
			$retval = $this->getContents();
		}
		else {
			$retval = $this->$name;
		}
        return $retval;
    }
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	/**
	 * Set an internal variable.
	 * 
	 * @param type $name
	 * @param type $val
	 * @throws InvalidArgumentException
	 */
	public function __set($name, $val=null) {
		switch($name) {
			case 'title':
				$this->title = $val;
				break;
			case 'body':
				$this->body = $val;
				break;
			case 'url':
				$this->url = $val;
				break;
			case 'linkText':
				$this->linkText = $val;
				break;
			default:
				throw new InvalidArgumentException;
		}
		$this->save();
	}
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	public function save() {
		$_SESSION[self::SESSIONKEY] = $this->getContents();
	}
	//----------------------------------------------------------------------------
}