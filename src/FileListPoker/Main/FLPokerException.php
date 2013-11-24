<?php

namespace FileListPoker\Main;

class FLPokerException extends \Exception
{
    const ERROR = 1;
    const INVALID_REQUEST = 2;
    const SITE_DOWN = 3;
    
    private $type;
    
    public function __construct($message, $type)
    {
        parent::__construct($message, null, null);
        
        $this->type = $type;
    }
    
    public function getType()
    {
        return $this->type;
    }
}
