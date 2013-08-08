<?php

require_once 'Site.class.php';

require_once 'ExcelPage.class.php';

$site = new Site();

$htmlout = $site->getHeader('excel.view.php');

$htmlout .= '<div id="title">' . $site->getWord('menu_excel') . '</div>';

$htmlout .= '<div id="content">';

$excelPage = new ExcelPage();
$content = $excelPage->getContent();

$htmlout .= '<table class="presentation-table" style="width:100%">
			<tr>
			<th><strong>' . $site->getWord('excel_view_pokerstars_name') . '</strong></th>
			<th><strong>' . $site->getWord('excel_view_filelist_name') . '</strong></th>
			<th><strong>' . $site->getWord('excel_view_current_points') . '</strong></th>
			</tr>';

foreach ($content as $player)
{
	$htmlout .= '<tr>
				<td>' . $player['name_pokerstars'] . '</td>
				<td><a href="http://filelist.ro/userdetails.php?id=' . $player['id_filelist'] . '">' . $player['name_filelist'] . '</a></td>
				<td>' . $player['points'] . '</td>
				</tr>';	
}

$htmlout .= '</table>';

$htmlout .= '</div>';
	
$htmlout .= $site->getFooter();

$htmlout .= '</body></html>';
	
echo $htmlout;