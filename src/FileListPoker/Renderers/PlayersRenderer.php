<?php

namespace FileListPoker\Renderers;

use FileListPoker\Renderers\GeneralRenderer;
use FileListPoker\Main\Site;

class PlayersRenderer extends GeneralRenderer
{
    private $site;
    
    public function __construct(Site $site)
    {
        $this->site = $site;
    }
    
    public function render($content)
    {
        $out = '<table class="presentation-table" style="width:100%">
            <tr>
            <th><strong>Nr.</strong></th>
            <th><strong>' . $this->site->getWord('players_pokerstars_name') . '</strong></th>
            <th><strong>' . $this->site->getWord('players_filelist_name') . '</strong></th>
            <th><strong>' . $this->site->getWord('players_registration_date') . '</strong></th>
            <th><strong>' . $this->site->getWord('players_current_points') . '</strong></th>
            </tr>';
        
        $i = 1;
        foreach ($content as $player) {
            $namePokerStars = (is_null($player['name_pokerstars']) or empty($player['name_pokerstars'])) ?
                            '<span class="faded">unknown</span>' :
                            $player['name_pokerstars'];
            
            if (is_null($player['name_filelist']) or empty($player['name_filelist'])) {
                $nameFilelist = '<span class="faded">unknown</span>';
                $flURL = $nameFilelist;
            } else {
                $nameFilelist = $player['name_filelist'];
                $flURL = '<a href="http://filelist.ro/userdetails.php?id=' . $player['id_filelist'] . '">' .
                        $nameFilelist . '</a>';
            }
            
            if (is_null($player['year']) or empty($player['year'])) {
                $regDate = '<span class="faded">unknown</span>';
            } else {
                $regTime = mktime(0, 0, 0, $player['month'], $player['day'], $player['year']);
                $regDate = date('j F Y', $regTime);
                
                if ($this->site->getLanguage() !== 'en') {
                    $regDate = $this->translateDate($regDate, $this->site->getLanguage());
                }
            }

            $out .=
            '<tr' . ($player['member_type'] == 'admin' ? ' class="admin-marker"' : '') . '>
                <td>' . $i . '</td>
                <td><a href="player.php?id=' . $player['player_id'] . '">' . $namePokerStars . '</a></td>
                <td>' . $flURL . '</td>
                <td>' . $regDate . '</td>
                <td>' . $player['points'] . '</td>
            </tr>';

            $i++;
        }

        $out .= '</table>';
        
        return $out;
    }
}
