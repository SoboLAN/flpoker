<?php
	require_once 'Site.class.php';
	
	$site = new Site();
	
	$htmlout = $site->getHeader('contact.php');
	
	$htmlout .= '<div id="title">' . $site->getWord('menu_contact') . '</div>';
	
	$htmlout .= '<div id="content-narrower">';
	
	/*
	if ($site->getLanguage() == 'ro')
	{
		$htmlout .=
		'<p>Puteți folosi acest formular pentru a mă contacta legat de orice problemă legată de site sau informațiile găsite pe el.
		Totuși nu abuzați de acest feature, că ne supărăm.</p>';
	}
	else if ($site->getLanguage() == 'en')
	{
		$htmlout .=
		'<p>You can use this form to contact me about any problem regarding this site or the information found on here.
		Though, please don\'t abuse this feature.</p>';
	}*/
	
	$htmlout .= 'This feature is temporarily disabled.';	
	
	$htmlout .= '</div>';
	
	$htmlout .= $site->getFooter();

	$htmlout .= '</body></html>';
	
	echo $htmlout;