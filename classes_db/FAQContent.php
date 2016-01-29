<?php

namespace FileListPoker\Content;

use FileListPoker\Main\Dictionary;

class FAQContent
{
    public function getFAQContent($lang)
    {
        if (! Dictionary::isValidLanguage($lang)) {
            return array();
        }
        
        $content = file_get_contents('faq/faq.' . $lang . '.json');
        
        $decodedContent = json_decode($content, true);
        
        return $decodedContent;
    }
}
