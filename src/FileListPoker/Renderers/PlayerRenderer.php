<?php

namespace FileListPoker\Renderers;

use FileListPoker\Renderers\GeneralRenderer;
use FileListPoker\Main\Site;

class PlayerRenderer extends GeneralRenderer
{
    private $site;
    
    public function __construct(Site $site)
    {
        $this->site = $site;
    }
    
    public function renderGeneral($content)
    {
        if (empty ($content)) {
            return '';
        }
        
        $namePokerStars = (is_null($content['name_pokerstars']) or empty($content['name_pokerstars'])) ?
                            '<span class="faded">unknown</span>' :
                            $content['name_pokerstars'];
            
        if (is_null($content['name_filelist']) or empty($content['name_filelist'])) {
            $nameFilelist = '<span class="faded">unknown</span>';
            $flURL = $nameFilelist;
        } else {
            $nameFilelist = $content['name_filelist'];
            $flURL = '<a href="http://filelist.ro/userdetails.php?id=' . $content['id_filelist'] . '">' .
                    $nameFilelist . '</a>';
        }
        
        if (is_null($content['month']) or empty($content['month'])) {
            $regDate = '<span class="faded">unknown</span>';
        } else {
            $regDate = date('j F Y', mktime(0, 0, 0, $content['month'], $content['day'], $content['year']));
            
            if ($this->site->getLanguage() !== 'en') {
                $regDate = $this->translateDate($regDate, $this->site->getLanguage());
            }
        }
        
        $out =
        '<p>
            <span class="bigger_label">' . $this->site->getWord('player_tab_general_pname') . ': ' .
                $namePokerStars .
            '</span>
        </p>
        <p>
            <span class="bigger_label">' . $this->site->getWord('player_tab_general_fname') . ': ' .
                $flURL . '</span>
        </p>
        <p>
            <span class="bigger_label">' . $this->site->getWord('player_tab_general_regdate') . ': ' .
                $regDate .
            '</span>
        </p>
        <p>
            <span class="bigger_label">' . $this->site->getWord('player_tab_general_points') . ': ' .
                $content['points'] .
            '</span>
        </p>
        <p>
            <span class="bigger_label">' . $this->site->getWord('player_tab_general_allpoints') . ': ' .
                $content['points_all_time'] .
            '</span>
        </p>
        <p>
            <span class="bigger_label">' . $this->site->getWord('player_tab_general_ftables') . ': ' .
                $content['final_tables'] .
            '</span>
        </p>
        <p>
            <span class="bigger_label">' . $this->site->getWord('player_tab_general_gmedals') . ': ' .
                $content['gold_medals'] .
            '</span>
        </p>
        <p>
            <span class="bigger_label">' . $this->site->getWord('player_tab_general_smedals') . ': ' .
                $content['silver_medals'] .
            '</span>
        </p>
        <p>
            <span class="bigger_label">' . $this->site->getWord('player_tab_general_bmedals') . ': ' .
                $content['bronze_medals'] .
            '</span>
        </p>';
        
        return $out;
    }
    
    public function rendererTHistory($content)
    {
        if (empty ($content)) {
            return '';
        }
        
        $out = '<table class="presentation-table" style="width:90%; margin: 0 auto">
            <tr>
            <th><strong>' . $this->site->getWord('player_tournament_tournament') . '</strong></th>
            <th><strong>' . $this->site->getWord('player_tournament_points') . '</strong></th>
            <th><strong>' . $this->site->getWord('player_tournament_position') . '</strong></th>
            </tr>';

        foreach ($content as $tournament) {
            $tTime = mktime(0, 0, 0, $tournament['month'], $tournament['day'], $tournament['year']);
            $tDate = date('j F Y', $tTime);
            
            if ($this->site->getLanguage() !== 'en') {
                $tDate = $this->translateDate($tDate, $this->site->getLanguage());
            }
            
            $position = (is_null($tournament['position']) or empty($tournament['position'])) ?
                        '<span class="faded">unknown</span>' :
                        $tournament['position'];

            $out .=
            '<tr>
                <td><a href="tournament.php?id=' . $tournament['tournament_id'] . '">' . $tDate . '</a></td>
                <td>' . $tournament['points'] . '</td>
                <td>' . $position . '</td>
            </tr>';
        }

        $out .= '</table>';

        return $out;
    }
    
    public function renderBonuses($content)
    {
        if (empty ($content)) {
            return '';
        }
        
        $out = '<table class="presentation-table" style="width:90%; margin: 0 auto">
            <tr>
            <th><strong>' . $this->site->getWord('player_bonus_tournament') . '</strong></th>
            <th><strong>' . $this->site->getWord('player_bonus_date') . '</strong></th>
            <th><strong>' . $this->site->getWord('player_bonus_value') . '</strong></th>
            <th><strong>' . $this->site->getWord('player_bonus_description') . '</strong></th>
            </tr>';

        foreach ($content as $bonus) {
            $bDate = date('j F Y', mktime(0, 0, 0, $bonus['month'], $bonus['day'], $bonus['year']));
            
            if ($this->site->getLanguage() !== 'en') {
                $bDate = $this->translateDate($bDate, $this->site->getLanguage());
            }
            
            $description = (is_null($bonus['description']) or empty($bonus['description'])) ?
                            '<span class="faded">unknown</span>' :
                            $bonus['description'];

            $out .=
            '<tr>
                <td><a href="tournament.php?id=' . $bonus['tournament_id'] . '">' . $bonus['tournament_id'] . '</a></td>
                <td>' . $bDate . '</td>
                <td>' . $bonus['bonus_value'] . '</td>
                <td>' . $description . '</td>
            </tr>';
        }

        $out .= '</table>';

        return $out;
    }
    
    public function renderPrizes($content)
    {
        if (empty ($content)) {
            return '';
        }
        
        $out = '<table class="presentation-table" style="width:90%; margin: 0 auto">
            <tr>
            <th><strong>' . $this->site->getWord('player_prize_prize') . '</strong></th>
            <th><strong>' . $this->site->getWord('player_prize_date') . '</strong></th>
            <th><strong>' . $this->site->getWord('player_prize_cost') . '</strong></th>
            </tr>';

        foreach ($content as $prize) {
            if (is_null($prize['day']) or is_null($prize['month']) or is_null($prize['year'])) {
                $pDate = '<span class="faded">unknown</span>';
            } else {
                $pDate = date('j F Y', mktime(0, 0, 0, $prize['month'], $prize['day'], $prize['year']));
                
                if ($this->site->getLanguage() !== 'en') {
                    $pDate = $this->translateDate($pDate, $this->site->getLanguage());
                }
            }
            
            $prizeText = (is_null($prize['prize']) or empty($prize['prize'])) ?
                        '<span class="faded">unknown</span>' :
                        $prize['prize'];

            $out .=
            '<tr>
                <td>' . $prizeText . '</td>
                <td>' . $pDate . '</td>
                <td>' . $prize['cost'] . '</td>
            </tr>';
        }

        $out .= '</table>';

        return $out;
    }
}