<?php

namespace FileListPoker\Main;

class Dictionary
{
    private static $availableLangs = array('en', 'ro');
    
    private static $wordPath = 'wording/text.{lang}.json';
    
    private static $words = array();
    
    public static function getWord($word, $lang)
    {
        if (!in_array($lang, self::$availableLangs)) {
            return null;
        }
        
        if (! isset(self::$words[$lang])) {
            $jsonContent = file_get_contents(str_replace('{lang}', $lang, self::$wordPath));
            
            self::$words[$lang] = json_decode($jsonContent, true);
        }
        
        return self::$words[$lang][$word];
    }
    
    public static function isValidLanguage($lang)
    {
        return in_array($lang, self::$availableLangs);
    }
}
