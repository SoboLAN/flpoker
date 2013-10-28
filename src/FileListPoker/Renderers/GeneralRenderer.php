<?php

namespace FileListPoker\Renderers;

abstract class GeneralRenderer
{
	/**
	 * 
	 * @param string $date the date
	 * @param string $lang the new language. For now, only 'ro' is accepted
	 * @return string the new date translated in the new language. If the language is invalid, an empty
	 * string is returned.
	 */
	public function translateDate($date, $lang)
	{
		$newDate = '';
		if($lang == 'ro')
		{
			if (strpos($date, 'January') !== false)
			{
				$newDate = str_replace('January', 'Ianuarie', $date);
				return $newDate;
			}
			else if (strpos($date, 'February') !== false)
			{
				$newDate = str_replace('February', 'Februarie', $date);
				return $newDate;
			}
			else if (strpos($date, 'March') !== false)
			{
				$newDate = str_replace('March', 'Martie', $date);
				return $newDate;
			}
			else if (strpos($date, 'April') !== false)
			{
				$newDate = str_replace('April', 'Aprilie', $date);
				return $newDate;
			}
			else if (strpos($date, 'May') !== false)
			{
				$newDate = str_replace('May', 'Mai', $date);
				return $newDate;
			}
			else if (strpos($date, 'June') !== false)
			{
				$newDate = str_replace('June', 'Iunie', $date);
				return $newDate;
			}
			else if (strpos($date, 'July') !== false)
			{
				$newDate = str_replace('July', 'Iulie', $date);
				return $newDate;
			}
			else if (strpos($date, 'August') !== false)
			{
				$newDate = str_replace('August', 'August', $date);
				return $newDate;
			}
			else if (strpos($date, 'September') !== false)
			{
				$newDate = str_replace('September', 'Septembrie', $date);
				return $newDate;
			}
			else if (strpos($date, 'October') !== false)
			{
				$newDate = str_replace('October', 'Octombrie', $date);
				return $newDate;
			}
			else if (strpos($date, 'November') !== false)
			{
				$newDate = str_replace('November', 'Noiembrie', $date);
				return $newDate;
			}
			else if (strpos($date, 'December') !== false)
			{
				$newDate = str_replace('December', 'Decembrie', $date);
				return $newDate;
			}
		}
		
		return $newDate;
	}
	
	public function translateDay($date, $lang)
	{
		$newDate = '';
		if($lang == 'ro')
		{
			if (strpos($date, 'Monday') !== false)
			{
				$newDate = str_replace('Monday', 'Luni', $date);
				return $newDate;
			}
			else if (strpos($date, 'Tuesday') !== false)
			{
				$newDate = str_replace('Tuesday', 'Marți', $date);
				return $newDate;
			}
			else if (strpos($date, 'Wednesday') !== false)
			{
				$newDate = str_replace('Wednesday', 'Miercuri', $date);
				return $newDate;
			}
			else if (strpos($date, 'Thursday') !== false)
			{
				$newDate = str_replace('Thursday', 'Joi', $date);
				return $newDate;
			}
			else if (strpos($date, 'Friday') !== false)
			{
				$newDate = str_replace('Friday', 'Vineri', $date);
				return $newDate;
			}
			else if (strpos($date, 'Saturday') !== false)
			{
				$newDate = str_replace('Saturday', 'Sâmbătă', $date);
				return $newDate;
			}
			else if (strpos($date, 'Sunday') !== false)
			{
				$newDate = str_replace('Sunday', 'Duminică', $date);
				return $newDate;
			}
		}
		
		return $newDate;
	}
}