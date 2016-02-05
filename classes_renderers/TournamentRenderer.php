<?php

namespace FileListPoker\Renderers;

use FileListPoker\Renderers\GeneralRenderer;
use FileListPoker\Main\Site;
use FileListPoker\Main\Dictionary;

class TournamentRenderer extends GeneralRenderer
{
    private $site;
    
    public function __construct(Site $site)
    {
        $this->site = $site;
    }
    
    public function renderDetails($template, $content)
    {
        if (empty ($content)) {
            return '';
        }
        
        $tournamentTime = mktime(0, 0, 0, $content['month'], $content['day'], $content['year']);
        $tournamentDate = date('l, j F Y', $tournamentTime);
        
        if ($this->site->getLanguage() !== Dictionary::LANG_EN) {
            $tournamentDate = $this->translateDate($tournamentDate, $this->site->getLanguage());
            $tournamentDate = $this->translateDay($tournamentDate, $this->site->getLanguage());
        }
        
        $type = $content['tournament_type'] == 'regular' ? $this->site->getWord('tournament_regular') :
                                                           $this->site->getWord('tournament_special');
        
        if (! empty($content['duration'])) {
            $hours = (int) ($content['duration'] / 60);
            $minutes = $content['duration'] % 60;
            $durationValue = $hours . 'h, ' . $minutes . 'min';
        } else {
            $durationValue = '<span class="bigger_label faded">unknown</span>';
        }
        
        $detailsTpl = str_replace(
            array(
                '{tournament_details}',
                '{tournament_date}',
                '{tournamentDate}',
                '{tournament_type}',
                '{type}',
                '{tournament_nrplayers}',
                '{participants}',
                '{tournament_duration}',
                '{durationValue}'
            ),
            array(
                $this->site->getWord('tournament_details'),
                $this->site->getWord('tournament_date'),
                $tournamentDate,
                $this->site->getWord('tournament_type'),
                $type,
                $this->site->getWord('tournament_nrplayers'),
                $content['participants'],
                $this->site->getWord('tournament_duration'),
                $durationValue
            ),
            $template
        );

        return $detailsTpl;
    }
    
    public function renderResults($template, $content)
    {
        if (empty ($content)) {
            return '';
        }
        
        $resultsTpl = str_replace(
            array(
                '{tournament_results}',
                '{tournament_position}',
                '{tournament_player}',
                '{tournament_points}',
                '{tournament_knockouts}'
            ),
            array(
                $this->site->getWord('tournament_results'),
                $this->site->getWord('tournament_position'),
                $this->site->getWord('tournament_player'),
                $this->site->getWord('tournament_points'),
                $this->site->getWord('tournament_knockouts')
            ),
            $template
        );

        $resultsList = '';
        foreach ($content as $result) {
            if (isset ($result['player_id']) and isset ($result['name_pokerstars'])) {
                $player = '<a href="player.php?id=' . $result['player_id'] . '">' . $result['name_pokerstars'] . '</a>';
            } else {
                $player = '<span class="faded">unknown</span>';
            }
            
            $position = (is_null($result['position']) or empty($result['position'])) ?
                        '<span class="faded">unknown</span>' :
                        $result['position'];
            
            $knockouts = (is_null($result['kos']) || is_null($result['kos'])) ?
                        '<span class="faded">unknown</span>' :
                        $result['kos'];

            $resultsList .=
            '<tr>
                <td>' . $position . '</td>
                <td>' . $player . '</td>
                <td>' . $result['points'] . '</td>
                <td>' . $knockouts . '</td>
            </tr>';
        }

        return str_replace('{resultsList}', $resultsList, $resultsTpl);
    }
    
    public function renderBonuses($template, $content)
    {
        if (empty ($content)) {
            return '';
        }
        
        $bonusesTpl = str_replace(
            array(
                '{tournament_bonuses}',
                '{tournament_player}',
                '{tournament_bonus}',
                '{tournament_bonus_description}'
            ),
            array(
                $this->site->getWord('tournament_bonuses'),
                $this->site->getWord('tournament_player'),
                $this->site->getWord('tournament_bonus'),
                $this->site->getWord('tournament_bonus_description')
            ),
            $template
        );

        $bonusList = '';
        foreach ($content as $bonus) {
            $descr = isset ($bonus['bonus_description']) ?
                            $bonus['bonus_description'] :
                            '<span class="faded">unknown</span>';
            
            $player = '<a href="player.php?id=' . $bonus['player_id'] . '">' . $bonus['name_pokerstars'] . '</a>';
            
            $bonusList .=
            '<tr>
                <td>' . $player . '</td>
                <td>' . $bonus['bonus_value'] . '</td>
                <td>' . $descr . '</td>
            </tr>';
        }

        return str_replace('{bonusList}', $bonusList, $bonusesTpl);
    }
}
