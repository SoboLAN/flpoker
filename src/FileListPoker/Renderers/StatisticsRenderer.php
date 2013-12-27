<?php

namespace FileListPoker\Renderers;

use FileListPoker\Renderers\GeneralRenderer;
use FileListPoker\Main\Site;

/**
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
class StatisticsRenderer extends GeneralRenderer
{
    private $site;
    
    public function __construct(Site $site)
    {
        $this->site = $site;
    }
    
    public function renderGeneral($template, $content)
    {
        $percentage = number_format((100.0 * $content['total_spent']) / $content['total_points'], 2);
        
        $generalTpl = str_replace(
            array(
                '{statistics_general_totalpoints}',
                '{totalPoints}',
                '{statistics_general_spent}',
                '{totalSpent}',
                '{totalSpentPercentage}',
                '{statistics_general_outoftotal}',
                '{statistics_general_nrusers}',
                '{totalPlayers}',
                '{statistics_general_nrtournaments}',
                '{totalTournaments}'
            ),
            array(
                $this->site->getWord('statistics_general_totalpoints'),
                number_format($content['total_points']),
                $this->site->getWord('statistics_general_spent'),
                number_format($content['total_spent']),
                $percentage,
                $this->site->getWord('statistics_general_outoftotal'),
                $this->site->getWord('statistics_general_nrusers'),
                $content['total_players'],
                $this->site->getWord('statistics_general_nrtournaments'),
                $content['total_tournaments']
            ),
            $template
        );
        
        return $generalTpl;
    }
    
    public function renderTournamentGraph($template, $content)
    {
        $tournamentParticipants = array();
        foreach ($content as $tournament) {
            $tournamentParticipants[] =
                "[Date.UTC({$tournament['year']}, {$tournament['month']} - 1, 1), {$tournament['average_participants']}]";
        }

        $tournamentParticipants = implode(",\n", $tournamentParticipants);
        
        $tournamentsTpl = str_replace(
            array(
                '{statistics_tournaments_text}',
                '{statistics_tournaments_charttitle}',
                '{statistics_tournaments_chartsubtitle}',
                '{statistics_tournaments_playersline}',
                '{tournamentParticipants}'
            ),
            array(
                $this->site->getWord('statistics_tournaments_text'),
                $this->site->getWord('statistics_tournaments_charttitle'),
                $this->site->getWord('statistics_tournaments_chartsubtitle'),
                $this->site->getWord('statistics_tournaments_playersline'),
                $tournamentParticipants
            ),
            $template
        );

        return $tournamentsTpl;
    }
        
    public function renderRegistrationsGraph($template, $content)
    {
        $clubRegistrants = array();
        foreach ($content as $record) {
            $clubRegistrants[] =
            "[Date.UTC({$record['join_year']}, {$record['join_month']} - 1, 1), {$record['nr_players']}]";
        }
        
        $clubRegistrants = implode(",\n", $clubRegistrants);
        
        $registrationsTpl = str_replace(
            array(
                '{statistics_registrations_text}',
                '{statistics_registrations_charttitle}',
                '{statistics_registrations_chartsubtitle}',
                '{statistics_registrations_nrplayersline}',
                '{statistics_registrations_nrplayersline}',
                '{clubRegistrants}'
            ),
            array(
                $this->site->getWord('statistics_registrations_text'),
                $this->site->getWord('statistics_registrations_charttitle'),
                $this->site->getWord('statistics_registrations_chartsubtitle'),
                $this->site->getWord('statistics_registrations_nrplayersline'),
                $this->site->getWord('statistics_registrations_nrplayersline'),
                $clubRegistrants
            ),
            $template
        );
        
        return $registrationsTpl;
    }
    
    public function renderAggressionGraph($template, $content)
    {
        $aggressionFactors = array();
        foreach ($content as $factor) {
            $aggressionFactors[] =
            "[Date.UTC({$factor['tournament_year']}, {$factor['tournament_month']} - 1, 1), {$factor['aggression_factor']}]";
        }
        
        $aggressionFactors = implode(",\n", $aggressionFactors);
        
        $aggressionTpl = str_replace(
            array(
                '{statistics_aggresion_text}',
                '{statistics_aggresion_charttitle}',
                '{statistics_aggresion_chartsubtitle}',
                '{statistics_aggresion_avgaggressionfactor}',
                '{statistics_aggresion_avgaggressionfactor}',
                '{aggressionFactors}'
            ),
            array(
                $this->site->getWord('statistics_aggresion_text'),
                $this->site->getWord('statistics_aggresion_charttitle'),
                $this->site->getWord('statistics_aggresion_chartsubtitle'),
                $this->site->getWord('statistics_aggresion_avgaggressionfactor'),
                $this->site->getWord('statistics_aggresion_avgaggressionfactor'),
                $aggressionFactors
            ),
            $template
        );
        
        return $aggressionTpl;
    }
}
