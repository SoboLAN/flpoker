<?php

namespace FileListPoker\Main;

/**
 * Exception class for the FL Poker site.
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
class FLPokerException extends \Exception
{
    //use when there is an error
    const ERROR = 1;
    
    //use when an invalid request is made
    const INVALID_REQUEST = 2;
    
    //use when the site is offline
    const SITE_OFFLINE = 3;
    
    public function __construct($message, $code)
    {
        parent::__construct($message, $code, null);
    }
}
