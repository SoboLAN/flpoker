<?php

namespace FileListPoker\Renderers;

use FileListPoker\Renderers\GeneralRenderer;
use FileListPoker\Main\Site;

class RankingsRenderer extends GeneralRenderer
{
    private $site;
    
    public function __construct(Site $site)
    {
        $this->site = $site;
    }
    
    public function renderMostActivePlayers($template, $content)
    {
        $mostActiveTpl = str_replace(
            array(
                '{statistics_most_active_text}',
                '{statistics_mostactive_player}',
                '{statistics_mostactive_count}'
            ),
            array(
                $this->site->getWord('statistics_most_active_text'),
                $this->site->getWord('statistics_mostactive_player'),
                $this->site->getWord('statistics_mostactive_count')
            ),
            $template
        );

        $mostActiveList = '';
        $i = 1;
        foreach ($content as $player) {
            $mostActiveList .=
            '<tr>
                <td>' . $i . '</td>
                <td><a href="player.php?id=' . $player['player_id'] . '">' . $player['name_pokerstars'] . '</a></td>
                <td>' . $player['count'] . '</td>
            </tr>';

            $i++;
        }

        return str_replace('{topMostActiveList}', $mostActiveList, $mostActiveTpl);
    }
    
    public function renderTopAllTime($template, $content)
    {
        $allTimeTpl = str_replace(
            array(
                '{statistics_top_all_time_text}',
                '{players_pokerstars_name}',
                '{players_filelist_name}',
                '{players_points_all_time}'
            ),
            array(
                $this->site->getWord('statistics_top_all_time_text'),
                $this->site->getWord('players_pokerstars_name'),
                $this->site->getWord('players_filelist_name'),
                $this->site->getWord('players_points_all_time')
            ),
            $template
        );

        $topAllTimeList = '';
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
                $flURL = '<a href="http://filelist.io/userdetails.php?id=' . $player['id_filelist'] . '">' .
                        $nameFilelist . '</a>';
            }
            
            $topAllTimeList .=
            '<tr>
                <td>' . $i . '</td>
                <td><a href="player.php?id=' . $player['player_id'] . '">' . $namePokerStars . '</a></td>
                <td>' . $flURL . '</td>
                <td>' . $player['points'] . '</td>
            </tr>';

            $i++;
        }

        return str_replace('{topAllTime}', $topAllTimeList, $allTimeTpl);
    }
    
    public function render6Months($template, $content)
    {
        $sixMonthsTpl = str_replace(
            array(
                '{statistics_top_6_months_text}',
                '{statistics_6months_player}',
                '{statistics_6months_points}'
            ),
            array(
                $this->site->getWord('statistics_top_6_months_text'),
                $this->site->getWord('statistics_6months_player'),
                $this->site->getWord('statistics_6months_points')
            ),
            $template
        );

        $sixMonthsList = '';
        $i = 1;
        foreach ($content as $player) {
            $sixMonthsList .=
            '<tr>
                <td>' . $i . '</td>
                <td><a href="player.php?id=' . $player['player_id'] . '">' . $player['name_pokerstars'] . '</a></td>
                <td>' . $player['totalp'] . '</td>
            </tr>';

            $i++;
        }

        return str_replace('{topSixMonthsList}', $sixMonthsList, $sixMonthsTpl);
    }
    
    public function renderFinalTables($template, $content)
    {
        $fTablesTpl = str_replace(
            array(
                '{statistics_final_tables_text}',
                '{statistics_final_tables_player}',
                '{statistics_final_tables_tables}'
            ),
            array(
                $this->site->getWord('statistics_final_tables_text'),
                $this->site->getWord('statistics_final_tables_player'),
                $this->site->getWord('statistics_final_tables_tables')
            ),
            $template
        );

        $finalTablesList = '';
        $i = 1;
        foreach ($content as $player) {
            $finalTablesList .=
            '<tr>
                <td>' . $i . '</td>
                <td><a href="player.php?id=' . $player['player_id'] . '">' . $player['name_pokerstars'] . '</a></td>
                <td>' . $player['final_tables'] . '</td>
            </tr>';

            $i++;
        }

        return str_replace('{topFinalTablesList}', $finalTablesList, $fTablesTpl);
    }
}
