<?php
	require_once 'Site.class.php';
	
	$site = new Site();
	
	$htmlout = $site->getHeader('rules.php');
	
	$htmlout .= '<div id="title">' . $site->getWord('menu_rules') . '</div>';
	
	$htmlout .= '<div id="content-narrower">';
	
	/*
	if ($site->getLanguage() == 'ro')
	{
		$htmlout .=
		'<p>.</p>';
	}
	else if ($site->getLanguage() == 'en')
	{
		$htmlout .=
		'<p>.</p>';
	}*/
	
	$htmlout .= 'This feature is temporarily disabled.';	
	
	$htmlout .= '</div>';
	
	$htmlout .= $site->getFooter();

	$htmlout .= '</body></html>';
	
	echo $htmlout;