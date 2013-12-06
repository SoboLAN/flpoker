<?php

namespace FileListPoker\Main;

use FileListPoker\Main\FLPokerException;
use FileListPoker\Main\Logger;

/**
 * Class that handles wording functionality a.k.a. labels of different languages on the site.
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
class Dictionary
{
    private static $availableLangs = array('en', 'ro');
    
    private static $wordPath = 'wording/text.{lang}.json';
    
    //will contain the actual words
    //will be filled by doing lazy-loading a.k.a. if a word in Italian is never requested, the
    //file that contains Italian words is never read
    private static $words = array();
    
    /**
     * Returns the requested word in the specified language.
     * @param string $word the key of the word you want
     * @param string $lang the language in which you want the word. Currently supported: English and Romanian.
     * @return mixed the word in the requested language or null if the word or language don't exist.
     */
    public static function getWord($word, $lang)
    {
        if (! self::isValidLanguage($lang)) {
            return null;
        }
        
        if (! isset(self::$words[$lang])) {
            
            $fileName = str_replace('{lang}', $lang, self::$wordPath);
            
            if (! is_readable($fileName)) {
                $ex = new FLPokerException('translation file is inaccessible', FLPokerException::ERROR);
                Logger::log($ex->getMessage());
                throw $ex;
            }
            
            self::$words[$lang] = json_decode(file_get_contents($fileName), true);
            
            if (is_null(self::$words[$lang])) {
                $ex = new FLPokerException('translation file is corrupt', FLPokerException::ERROR);
                Logger::log($ex->getMessage());
                throw $ex;
            }
        }
        
        return isset(self::$words[$lang][$word]) ? self::$words[$lang][$word] : null;
    }
    
    /**
     * Tells whether the specified language is supported by this Dictionary.
     * @param string $lang the language
     * @return bool true if the specified language is supported, false otherwise.
     */
    public static function isValidLanguage($lang)
    {
        return in_array($lang, self::$availableLangs);
    }
}
