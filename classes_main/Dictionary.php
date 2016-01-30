<?php

namespace FileListPoker\Main;

use FileListPoker\Main\FLPokerException;

use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Class that handles wording functionality a.k.a. labels of different languages on the site.
 */
class Dictionary
{
    const LANG_RO = 'ro';
    const LANG_EN = 'en';
    
    private static $wordPath = 'wording/text.{lang}.json';
    
    //will contain the actual words
    //will be filled by doing lazy-loading a.k.a. if a word in Italian is never requested, the
    //file that contains Italian words is never read
    private static $words = array();
    
    //since validation of language is slightly expensive and, since it's done very often,
    //this field will "cache" the validation results
    private static $validatedLanguages = array();
    
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
                throw new FLPokerException('translation file is inaccessible', FLPokerException::ERROR);
            }
            
            self::$words[$lang] = json_decode(file_get_contents($fileName), true);
            
            if (is_null(self::$words[$lang])) {
                throw new FLPokerException('translation file is corrupt', FLPokerException::ERROR);
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
        if (isset(self::$validatedLanguages[$lang])) {
            return self::$validatedLanguages[$lang];
        }

        $validatorBuilder = new ValidatorBuilder();

        /* @var $validator ValidatorInterface */
        $validator = $validatorBuilder->getValidator();

        $choiceConstraint = new Choice();
        $choiceConstraint->choices = array(self::LANG_EN, self::LANG_RO);

        $notNullConstraint = new NotNull();

        $errors = $validator->validate($lang, array($choiceConstraint, $notNullConstraint));

        $isValid = count($errors) === 0;

        self::$validatedLanguages[$lang] = $isValid;

        return $isValid;
    }
}
