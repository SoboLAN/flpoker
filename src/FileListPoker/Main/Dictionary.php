<?php

namespace FileListPoker\Main;

class Dictionary
{
    private static $availableLangs = array('en', 'ro');
    
    private static $wordPath = 'wording/text.{lang}.json';
    
    private static $words = array();
    
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
        
        return self::$words[$lang][$word];
    }
    
    public static function isValidLanguage($lang)
    {
        return in_array($lang, self::$availableLangs);
    }
}
