<?php
require_once 'Site.class.php';

$site = new Site();

$htmlout = $site->getHeader('rules.php');

$htmlout .= '<div id="title">' . $site->getWord('menu_rules') . '</div>';

$htmlout .= '<div id="content-narrower">';

/*$htmlout .= '
<div id="accordion" style="margin: 0 auto;">
	<h3><a href="#">Reguli Generale</a></h3>
	<div>
		Version 2.2 of Poker Enlighter is now available for download.
		<p>This version fixes a critical bug which causes incorrect results in Omaha Hi/Lo simulations,
		improves the XML export functionality, upgrades some of the 3rd-party libraries and adds
		some other small improvements.</p>
		Check the <a href="changelog.php">Changelog</a> for more details.
	</div>
	
</div>';*/

$htmlout .= 'This feature is temporarily disabled.';

$htmlout .= '</div>';

$htmlout .= $site->getFooter();

$htmlout .= '</body></html>';

echo $htmlout;