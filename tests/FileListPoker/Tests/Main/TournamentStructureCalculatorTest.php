<?php

namespace FileListPoker\Tests\Main;

use FileListPoker\Main\TournamentStructureCalculator;

class TournamentStructureCalculatorTest extends \PHPUnit_Framework_TestCase
{
    public function numberOfPlayersProvider()
    {
        return array(
            array(12, 12),
            array(13, 12),
            array(14, 12),
            array(15, 12),
            array(16, 12),
            array(17, 12),
            array(18, 12),
            array(19, 12),
            array(20, 12),
            array(21, 12),
            array(22, 12),
            array(23, 12),
            array(24, 12),
            array(25, 13),
            array(26, 13),
            array(27, 14),
            array(28, 14),
            array(29, 15),
            array(30, 15),
            array(31, 16),
            array(32, 16),
            array(33, 17),
            array(34, 17),
            array(35, 18),
            array(36, 18),
            array(37, 19),
            array(38, 19),
            array(39, 19),
            array(40, 19),
            array(41, 19),
            array(42, 19),
            array(43, 19),
            array(44, 19),
            array(45, 19)
        );
    }
    
    /**
     * @dataProvider numberOfPlayersProvider
     */
    public function testGetNumberOfPayedPlayers($nrPlayers, $expectedPayedPositions)
    {
        $instance = new TournamentStructureCalculator();
        
        $this->assertEquals($expectedPayedPositions, $instance->getNumberOfPayedPlayers($nrPlayers));
    }
    
    public function payedPositionsProvider()
    {
        $positions1 = array();
        for ($i = 1; $i <= 14; $i++) {
            $positions1[] = $i;
        }
        $expectedResult1 = array(
            1 => 19,
            2 => 17,
            3 => 15,
            4 => 13,
            5 => 10,
            6 => 9,
            7 => 8,
            8 => 7,
            9 => 6,
            10 => 5,
            11 => 4,
            12 => 3,
            13 => 2,
            14 => 1
        );
        
        $positions2 = array();
        for ($i = 1; $i <= 99; $i++) {
            $positions2[] = $i;
        }
        $expectedResult2 = null;
        
        $positions3 = array(
            1,
            2,
            3,
            4,
            7,
            8,
            9,
            10,
            12,
            13,
            14,
            16
        );
        
        $expectedResult3 = array(
            1 => 15,
            2 => 13,
            3 => 10,
            4 => 9,
            7 => 8,
            8 => 7,
            9 => 6,
            10 => 5,
            12 => 4,
            13 => 3,
            14 => 2,
            16 => 1
        );
        
        return array(
            'normal 14 positions' => array($positions1, $expectedResult1),
            'count of positions higher than available prizes' => array($positions2, $expectedResult2),
            'multiple positions missing from various places' => array($positions3, $expectedResult3)
        );
    }
    
    /**
     * @dataProvider payedPositionsProvider
     */
    public function testGetPayedPositions($positions, $expectedResult)
    {
        $instance = new TournamentStructureCalculator();
        
        $this->assertEquals($expectedResult, $instance->getPayedPositions($positions));
    }
}
