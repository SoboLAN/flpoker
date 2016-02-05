<?php

namespace FileListPoker\Renderers;

use FileListPoker\Renderers\GeneralRenderer;
use FileListPoker\Main\Site;
use FileListPoker\Main\Config;

class StatusRenderer extends GeneralRenderer
{
    private $site;
    
    public function __construct(Site $site)
    {
        $this->site = $site;
    }
    
    public function rendererFinalTables($template, $content)
    {
        if (empty ($content)) {
            return '';
        }
        
        $fTablesTpl = str_replace(
            array(
                '{status_ftables_text}',
                '{status_ftables_player}',
                '{status_ftables_ftables}'
            ),
            array(
                $this->site->getWord('status_ftables_text'),
                $this->site->getWord('status_ftables_player'),
                $this->site->getWord('status_ftables_ftables')
            ),
            $template
        );

        $fTablesList = '';
        foreach ($content as $fTable) {
            
            $fTablesList .=
            '<tr>
                <td><a href="player.php?id=' . $fTable['player_id'] . '">' . $fTable['name_pokerstars'] . '</a></td>
                <td>' . $fTable['final_tables'] . '</td>
            </tr>';
        }
        
        return str_replace('{player_ftables_list}', $fTablesList, $fTablesTpl);
    }
    
    public function renderCurrentStandings($template, $content)
    {
        if (empty ($content)) {
            return '';
        }
        
        $standingsTpl = str_replace(
            array(
                '{status_standings_text}',
                '{status_standings_position}',
                '{status_standings_player}',
                '{status_standings_points}'
            ),
            array(
                $this->site->getWord('status_standings_text'),
                $this->site->getWord('status_standings_position'),
                $this->site->getWord('status_standings_player'),
                $this->site->getWord('status_standings_points')
            ),
            $template
        );
        
        $bonusList = '';
        $siteURL = Config::getValue('site_url');
        for ($i = 0; $i < count($content); $i++) {
        
            if (count($content[$i]) > 1) {

                $currentPlayers = array();
                foreach ($content[$i] as $player) {
                    $currentPlayers[] = '<a href="' . $siteURL . 'player.php?id=' . $player['player_id'] . '">' . $player['name_pokerstars'] . '</a>';
                }

                $bonusList .= '<tr>
                    <td>' . ($i + 1) . '</td>
                    <td>' . implode(', ', $currentPlayers) . '</td>
                    <td>' . $content[$i][0]['points'] . '</td>
                </tr>';
            } else {
                $bonusList .= '<tr>
                <td>' . ($i + 1) . '</td>
                <td><a href="' . $siteURL . 'player.php?id=' . $content[$i][0]['player_id'] . '">' . $content[$i][0]['name_pokerstars'] . '</a></td>
                <td>' . $content[$i][0]['points'] . '</td></tr>';
            }
        }
        
        return str_replace('{player_standings_list}', $bonusList, $standingsTpl);
    }
}
