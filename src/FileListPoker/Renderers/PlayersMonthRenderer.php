<?php

namespace FileListPoker\Renderers;

use FileListPoker\Renderers\GeneralRenderer;
use FileListPoker\Main\Site;

class PlayersMonthRenderer extends GeneralRenderer
{
    private $site;
    
    public function __construct(Site $site)
    {
        $this->site = $site;
    }
    
    public function render($template, $content)
    {
        if (empty ($content)) {
            return '';
        }
        
        $pMonthTpl = str_replace(
            array(
                '{players_month_pokerstars}',
                '{players_month_filelist}',
                '{players_month_date}'
            ),
            array(
                $this->site->getWord('players_month_pokerstars'),
                $this->site->getWord('players_month_filelist'),
                $this->site->getWord('players_month_date')
            ),
            $template
        );
        
        $playersList = '';
        foreach ($content as $award) {
            $awardTime = mktime(0, 0, 0, $award['month'], 2, $award['year']);
            $awardDate = date('F Y', $awardTime);
            
            if ($this->site->getLanguage() !== 'en') {
                $awardDate = $this->translateDate($awardDate, $this->site->getLanguage());
            }

            $playersList .=
            '<tr>
                <td><a href="player.php?id=' . $award['id'] . '">' . $award['name_pokerstars'] . '</a></td>
                <td>
                    <a href="http://filelist.ro/userdetails.php?id=' . $award['id_filelist'] . '">' .
                    $award['name_filelist'] . '</a>
                </td>
                <td>' . $awardDate . '</td>
            </tr>';
        }

        return str_replace('{players_of_the_month_list}', $playersList, $pMonthTpl);
    }
}
