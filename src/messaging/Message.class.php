<?php

namespace crazedsanity\messaging;

use \InvalidArgumentException;

class Message {
    
    const TYPE_NOTICE = "notice";
    const TYPE_STATUS = "status";
    const TYPE_ERROR  = "error";
    const TYPE_FATAL  = "fatal";
	
	CONST SESSIONKEY = 'message';
    
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
    public function __construct() {
		$this->type = self::DEFAULT_TYPE;
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
	 * Set an internal variable, and data is saved (if it is valid)
	 * 
	 * @param type $name
	 * @param type $val
	 */
	public function __set($name, $val=null) {
		if($this->validate($name, $val)) {
			$this->$name = $val;
		}
		try {
			$this->save();
		}
		catch(InvalidArgumentException $e) {
			//
		}
	}
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	public function validate($field, $val) {
		switch($field) {
			case 'title':
				if(strlen($val) <= 2) {
					throw new InvalidArgumentException("invalid title");
				}
				break;
			case 'body':
				if(is_null($val) || strlen($val) <= 2) {
					throw new InvalidArgumentException("invalid ". $field);
				}
				break;
			case 'type':
				if(is_null($val) || !in_array($val, self::$validTypes)) {
					throw new InvalidArgumentException("invalid type ($val)");
				}
				break;
			case 'url':
			case 'linkText':
				break;
			default:
				throw new InvalidArgumentException("invalid field");
		}
		
		// got this far without an exception? good to go.
		return true;
	}
	//----------------------------------------------------------------------------
	
	
	
	//----------------------------------------------------------------------------
	public function save() {
		$fields = array('title', 'body', 'type', 'url', 'linkText');
		foreach($fields as $k) {
			$this->validate($k, $this->$k);
		}
		$_SESSION[self::SESSIONKEY] = $this->getContents();
	}
	//----------------------------------------------------------------------------
}