<?php

namespace FileListPoker\Renderers;

use FileListPoker\Main\Dictionary;

/**
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
abstract class GeneralRenderer
{
    /**
     * 
     * @param string $date the date
     * @param string $lang the new language. For now, only 'ro' is accepted
     * @return string the new date translated in the new language. If the language is invalid, an empty
     * string is returned.
     */
    protected function translateDate($date, $lang)
    {
        $monthTranslations = array(
            'January' => 'Ianuarie',
            'February' => 'Februarie',
            'March' => 'Martie',
            'April' => 'Aprilie',
            'May' => 'Mai',
            'June' => 'Iunie',
            'July' => 'Iulie',
            'August' => 'August',
            'September' => 'Septembrie',
            'October' => 'Octombrie',
            'November' => 'Noiembrie',
            'December' => 'Decembrie'
        );
        
        return $this->translateDatePart($date, $lang, $monthTranslations);
    }
    
    protected function translateDay($date, $lang)
    {
        $dayTranslations = array(
            'Monday' => 'Luni',
            'Tuesday' => 'Marți',
            'Wednesday' => 'Miercuri',
            'Thursday' => 'Joi',
            'Friday' => 'Vineri',
            'Saturday' => 'Sâmbătă',
            'Sunday' => 'Duminică'
        );
        
        return $this->translateDatePart($date, $lang, $dayTranslations);
    }
    
    protected function translateMemberType($memberType, $lang)
    {
        if ($lang == Dictionary::LANG_RO) {
            if ($memberType == 'admin') {
                return 'Administrator';
            } elseif ($memberType == 'regular') {
                return 'Cont obișnuit';
            }
        } elseif ($lang == Dictionary::LANG_EN) {
            if ($memberType == 'admin') {
                return 'Administrator';
            } elseif ($memberType == 'regular') {
                return 'Regular Member';
            }
        }
    }
    
    private function translateDatePart($date, $lang, array $translationMap)
    {
        if ($lang == Dictionary::LANG_RO) {
            foreach ($translationMap as $englishName => $romanianName) {
                if (strpos($date, $englishName) !== false) {
                    return str_replace($englishName, $romanianName, $date);
                }
            }
        }
        
        return '';
    }
}
