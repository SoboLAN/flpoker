<?php

namespace FileListPoker\Renderers;

use FileListPoker\Renderers\GeneralRenderer;
use FileListPoker\Main\Site;

class TournamentsRenderer extends GeneralRenderer
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
        
        $tournamentsTpl = str_replace(
            array(
                '{tournaments_tournament_date}',
                '{tournaments_type}',
                '{tournaments_participants}'
            ),
            array(
                $this->site->getWord('tournaments_tournament_date'),
                $this->site->getWord('tournaments_type'),
                $this->site->getWord('tournaments_participants')
            ),
            $template
        );
        
        $tournamentsList = '';
        foreach ($content as $tournament) {
            $typeLabelKey = $tournament['type'] == 'regular' ? 'tournaments_regular' : 'tournaments_special';

            $tournamentTime = mktime(0, 0, 0, $tournament['month'], $tournament['day'], $tournament['year']);
            $tournamentDate = date('l, j F Y', $tournamentTime);
            
            if ($this->site->getLanguage() !== 'en') {
                $tournamentDate = $this->translateDate($tournamentDate, $this->site->getLanguage());
                $tournamentDate = $this->translateDay($tournamentDate, $this->site->getLanguage());
            }

            $tournamentsList .=
            '<tr>
                <td>' . $tournamentDate . '</td>
                <td>' . $this->site->getWord($typeLabelKey) . '</td>
                <td>' . $tournament['participants'] . '</td>
                <td>
                    <a href="tournament.php?id=' . $tournament['id'] . '">' .
                        $this->site->getWord('tournaments_more_details') . '
                    </a>
                </td>
            </tr>';
        }

        return str_replace('{tournaments_list}', $tournamentsList, $tournamentsTpl);
    }
}
