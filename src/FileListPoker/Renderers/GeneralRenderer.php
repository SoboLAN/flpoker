<?php

namespace FileListPoker\Renderers;

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
        $newDate = '';
        if ($lang == 'ro') {
            if (strpos($date, 'January') !== false) {
                $newDate = str_replace('January', 'Ianuarie', $date);
                return $newDate;
            } elseif (strpos($date, 'February') !== false) {
                $newDate = str_replace('February', 'Februarie', $date);
                return $newDate;
            } elseif (strpos($date, 'March') !== false) {
                $newDate = str_replace('March', 'Martie', $date);
                return $newDate;
            } elseif (strpos($date, 'April') !== false) {
                $newDate = str_replace('April', 'Aprilie', $date);
                return $newDate;
            } elseif (strpos($date, 'May') !== false) {
                $newDate = str_replace('May', 'Mai', $date);
                return $newDate;
            } elseif (strpos($date, 'June') !== false) {
                $newDate = str_replace('June', 'Iunie', $date);
                return $newDate;
            } elseif (strpos($date, 'July') !== false) {
                $newDate = str_replace('July', 'Iulie', $date);
                return $newDate;
            } elseif (strpos($date, 'August') !== false) {
                $newDate = str_replace('August', 'August', $date);
                return $newDate;
            } elseif (strpos($date, 'September') !== false) {
                $newDate = str_replace('September', 'Septembrie', $date);
                return $newDate;
            } elseif (strpos($date, 'October') !== false) {
                $newDate = str_replace('October', 'Octombrie', $date);
                return $newDate;
            } elseif (strpos($date, 'November') !== false) {
                $newDate = str_replace('November', 'Noiembrie', $date);
                return $newDate;
            } elseif (strpos($date, 'December') !== false) {
                $newDate = str_replace('December', 'Decembrie', $date);
                return $newDate;
            }
        }
        
        return $newDate;
    }
    
    protected function translateDay($date, $lang)
    {
        $newDate = '';
        if ($lang == 'ro') {
            if (strpos($date, 'Monday') !== false) {
                $newDate = str_replace('Monday', 'Luni', $date);
                return $newDate;
            } elseif (strpos($date, 'Tuesday') !== false) {
                $newDate = str_replace('Tuesday', 'Marți', $date);
                return $newDate;
            } elseif (strpos($date, 'Wednesday') !== false) {
                $newDate = str_replace('Wednesday', 'Miercuri', $date);
                return $newDate;
            } elseif (strpos($date, 'Thursday') !== false) {
                $newDate = str_replace('Thursday', 'Joi', $date);
                return $newDate;
            } elseif (strpos($date, 'Friday') !== false) {
                $newDate = str_replace('Friday', 'Vineri', $date);
                return $newDate;
            } elseif (strpos($date, 'Saturday') !== false) {
                $newDate = str_replace('Saturday', 'Sâmbătă', $date);
                return $newDate;
            } elseif (strpos($date, 'Sunday') !== false) {
                $newDate = str_replace('Sunday', 'Duminică', $date);
                return $newDate;
            }
        }
        
        return $newDate;
    }
    
    protected function translateMemberType($memberType, $lang)
    {
        if ($lang == 'ro') {
            if ($memberType == 'admin') {
                return 'Administrator';
            } elseif ($memberType == 'regular') {
                return 'Cont obișnuit';
            }
        } elseif ($lang == 'en') {
            if ($memberType == 'admin') {
                return 'Administrator';
            } elseif ($memberType == 'regular') {
                return 'Regular Member';
            }
        }
        
    }
}
