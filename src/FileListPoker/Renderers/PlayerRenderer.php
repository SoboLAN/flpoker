<?php

namespace FileListPoker\Renderers;

use FileListPoker\Renderers\GeneralRenderer;
use FileListPoker\Main\Site;

/**
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
class PlayerRenderer extends GeneralRenderer
{
    private $site;
    
    public function __construct(Site $site)
    {
        $this->site = $site;
    }
    
    public function renderGeneral($template, $content)
    {
        $namePokerStars = (is_null($content['name_pokerstars']) or empty($content['name_pokerstars'])) ?
                            '<span class="faded">unknown</span>' :
                            $content['name_pokerstars'];
            
        if (is_null($content['name_filelist']) or empty($content['name_filelist'])) {
            $nameFilelist = '<span class="faded">unknown</span>';
        } else {
            $nameFilelist = $content['name_filelist'];
        }
        
        if (is_null($content['month']) or empty($content['month'])) {
            $regDate = '<span class="faded">unknown</span>';
        } else {
            $regDate = date('j F Y', mktime(0, 0, 0, $content['month'], $content['day'], $content['year']));
            
            if ($this->site->getLanguage() !== 'en') {
                $regDate = $this->translateDate($regDate, $this->site->getLanguage());
            }
        }
        
        $memberType = $this->translateMemberType($content['member_type'], $this->site->getLanguage());
        
        $result = str_replace(
            array(
                '{player_tab_general_pname}',
                '{pname}',
                '{player_tab_general_fname}',
                '{flname}',
                '{player_tab_general_membertype}',
                '{member_type}',
                '{flid}',
                '{player_tab_general_regdate}',
                '{regdate}',
                '{player_tab_general_points}',
                '{general_points}',
                '{player_tab_general_allpoints}',
                '{all_points}',
                '{player_tab_general_ftables}',
                '{ftables}',
                '{player_tab_general_gmedals}',
                '{gmedals}',
                '{player_tab_general_smedals}',
                '{smedals}',
                '{player_tab_general_bmedals}',
                '{bmedals}'
            ),
            array(
                $this->site->getWord('player_tab_general_pname'),
                $namePokerStars,
                $this->site->getWord('player_tab_general_fname'),
                $nameFilelist,
                $this->site->getWord('player_tab_general_membertype'),
                $memberType,
                $content['id_filelist'],
                $this->site->getWord('player_tab_general_regdate'),
                $regDate,
                $this->site->getWord('player_tab_general_points'),
                $content['points'],
                $this->site->getWord('player_tab_general_allpoints'),
                $content['points_all_time'],
                $this->site->getWord('player_tab_general_ftables'),
                $content['final_tables'],
                $this->site->getWord('player_tab_general_gmedals'),
                $content['gold_medals'],
                $this->site->getWord('player_tab_general_smedals'),
                $content['silver_medals'],
                $this->site->getWord('player_tab_general_bmedals'),
                $content['bronze_medals']
            ),
            $template
        );
        
        return $result;
    }
    
    public function rendererTHistory($template, $content)
    {
        if (empty ($content)) {
            return '';
        }
        
        $tHistoryTpl = $template;
        
        $tHistoryTpl = str_replace(
            array(
                '{player_tournament_tournament}',
                '{player_tournament_points}',
                '{player_tournament_position}'
            ),
            array(
                $this->site->getWord('player_tournament_tournament'),
                $this->site->getWord('player_tournament_points'),
                $this->site->getWord('player_tournament_position')
            ),
            $tHistoryTpl
        );

        $tournamentsList = '';
        foreach ($content as $tournament) {
            $tTime = mktime(0, 0, 0, $tournament['month'], $tournament['day'], $tournament['year']);
            $tDate = date('j F Y', $tTime);
            
            if ($this->site->getLanguage() !== 'en') {
                $tDate = $this->translateDate($tDate, $this->site->getLanguage());
            }
            
            $position = (is_null($tournament['position']) or empty($tournament['position'])) ?
                        '<span class="faded">unknown</span>' :
                        $tournament['position'];

            $tournamentsList .=
            '<tr>
                <td><a href="tournament.php?id=' . $tournament['tournament_id'] . '">' . $tDate . '</a></td>
                <td>' . $tournament['points'] . '</td>
                <td>' . $position . '</td>
            </tr>';
        }
        
        return str_replace('{player_tournament_list}', $tournamentsList, $tHistoryTpl);
    }
    
    public function renderBonuses($template, $content)
    {
        if (empty ($content)) {
            return '';
        }
        
        $bonusesTpl = str_replace(
            array(
                '{player_bonus_tournament}',
                '{player_bonus_date}',
                '{player_bonus_value}',
                '{player_bonus_description}'
            ),
            array(
                $this->site->getWord('player_bonus_tournament'),
                $this->site->getWord('player_bonus_date'),
                $this->site->getWord('player_bonus_value'),
                $this->site->getWord('player_bonus_description')
            ),
            $template
        );
        
        $bonusList = '';
        foreach ($content as $bonus) {
            $bDate = date('j F Y', mktime(0, 0, 0, $bonus['month'], $bonus['day'], $bonus['year']));
            
            if ($this->site->getLanguage() !== 'en') {
                $bDate = $this->translateDate($bDate, $this->site->getLanguage());
            }
            
            $description = (is_null($bonus['description']) or empty($bonus['description'])) ?
                            '<span class="faded">unknown</span>' :
                            $bonus['description'];

            $bonusList .=
            '<tr>
                <td><a href="tournament.php?id=' . $bonus['tournament_id'] . '">' . $bonus['tournament_id'] . '</a></td>
                <td>' . $bDate . '</td>
                <td>' . $bonus['bonus_value'] . '</td>
                <td>' . $description . '</td>
            </tr>';
        }
        
        return str_replace('{player_bonuses_list}', $bonusList, $bonusesTpl);
    }
    
    public function renderPrizes($template, $content)
    {
        if (empty ($content)) {
            return '';
        }
        
        $prizesTpl = $template;
        
        $prizesTpl = str_replace(
            array(
                '{player_prize_prize}',
                '{player_prize_date}',
                '{player_prize_cost}',
            ),
            array(
                $this->site->getWord('player_prize_prize'),
                $this->site->getWord('player_prize_date'),
                $this->site->getWord('player_prize_cost'),
            ),
            $prizesTpl
        );
        
        $prizeList = '';
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

            $prizeList .=
            '<tr>
                <td>' . $prizeText . '</td>
                <td>' . $pDate . '</td>
                <td>' . $prize['cost'] . '</td>
            </tr>';
        }

        return str_replace('{player_prizes_list}', $prizeList, $prizesTpl);
    }
}
