<?php

namespace FileListPoker\Main;

use FileListPoker\Main\Config;
use FileListPoker\Main\Dictionary;
use FileListPoker\Main\FLPokerException;

/**
 * Main class of the site. Handles dependencies, Google Analytics
 * and is responsible for rendering the common blocks of the site
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
class Site
{
    //current language of the site
    private $lang;
    
    public function __construct()
    {
        if (! Config::getValue('online')) {
            throw new FLPokerException('The site is currently down for maintenance', FLPokerException::SITE_OFFLINE);
        }

        $cookieName = Config::getValue('lang_cookie_name');
    
        //read language from the cookie or use default language and write it to the cookie to be used
        //on the next request
        if (isset ($_COOKIE[$cookieName]) and Dictionary::isValidLanguage($_COOKIE[$cookieName])) {
            $this->lang = $_COOKIE[$cookieName];
        } else {
            $this->lang = Config::getValue('default_lang');
            
            $cookieDuration = Config::getValue('lang_cookie_duration');
            
            setcookie($cookieName, $this->lang, time() + $cookieDuration);
        }
    }
    
    /**
     * Get the language the site is currently using.
     * @return string a valid language
     */
    public function getLanguage()
    {
        return $this->lang;
    }
    
    /**
     * Get the word specified by the key in the site's current language.
     * @param string $key the word key
     * @return mixed a string representing the desired word or null if it doesn't exist.
     */
    public function getWord($key)
    {
        return Dictionary::getWord($key, $this->lang);
    }
    
    /**
     * Utility function that tells you whether the specified ID is valid.
     * A valid ID is made up only of digits, maximum 4 of them.
     * @param int $id the ID.
     * @return bool true if the specified ID is valid, false otherwise.
     */
    public function isValidID($id)
    {
        return (strlen($id) <= 4 and ctype_digit($id));
    }
}
