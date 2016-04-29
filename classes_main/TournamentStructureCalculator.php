<?php

namespace FileListPoker\Main;

class TournamentStructureCalculator
{
    private $paymentStructure = array(
        1 => 45,
        2 => 42,
        3 => 40,
        4 => 37,
        5 => 35,
        6 => 32,
        7 => 30,
        8 => 27,
        9 => 25,
        10 => 22,
        11 => 20,
        12 => 17,
        13 => 15,
        14 => 12,
        15 => 10,
        16 => 9,
        17 => 8,
        18 => 7,
        19 => 6,
        20 => 5,
        21 => 4,
        22 => 3,
        23 => 2,
        24 => 1
    );
    
    public function getNumberOfPayedPlayers($nrParticipants)
    {
        return min(
            array(
                max(
                    array(
                        floor($nrParticipants / 2),
                        12
                    )
                ),
                count($this->paymentStructure)
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
