<?php

namespace FileListPoker\Main;

class TournamentStructureCalculator
{
    private $paymentStructure = array(
        1 => 30,
        2 => 27,
        3 => 25,
        4 => 23,
        5 => 21,
        6 => 19,
        7 => 17,
        8 => 15,
        9 => 13,
        10 => 10,
        11 => 9,
        12 => 8,
        13 => 7,
        14 => 6,
        15 => 5,
        16 => 4,
        17 => 3,
        18 => 2,
        19 => 1
    );
    
    public function getNumberOfPayedPlayers($nrParticipants)
    {
        return min(
            array(
                max(
                    array(
                        ceil($nrParticipants / 2),
                        12
                    )
                ),
                19
            )
        );
    }
    
    public function getPayedPositions(array $positions)
    {
        if (count($positions) > count($this->paymentStructure)) {
            return null;
        }
        
        $paymentStructureSubset = array_slice(
            $this->paymentStructure,
            count($this->paymentStructure) - count($positions)
        );
        
        $result = array();
        for ($i = 0; $i < count($positions); $i++) {
            $result[$positions[$i]] = $paymentStructureSubset[$i];
        }
        
        return $result;
    }
}
