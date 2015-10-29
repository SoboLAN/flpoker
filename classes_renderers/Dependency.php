<?php

namespace FileListPoker\Renderers;

class Dependency
{
    const GENERAL_CSS = 'path_general_css';
    const JQUERY = 'path_jquery';
    const JQUERY_UI = 'path_jqueryui';
    const JQUERY_UI_CSS = 'path_jqueryui_css';
    const HIGHCHARTS = 'path_highcharts';
    const HIGHCHARTS_THEME = 'path_highcharts_theme';
    
    const TYPE_JS = '<script src="{place}"></script>';
    const TYPE_CSS = '<link rel="stylesheet" type="text/css" href="{place}" />';
    
    public static $pages = array (
        'index.php'         => 'getIndex',
        'status.php'        => 'getStatus',
        'players.php'       => 'getPlayers',
        'tournaments.php'   => 'getTournaments',
        'rankings.php'      => 'getRankings',
        'statistics.php'    => 'getStatistics',
        'players.month.php' => 'getPlayersOfTheMonth'
    );
    
    private $name;
    private $type;
    
    private function __construct($name, $type)
    {
        $this->name = $name;
        $this->type = $type;
    }
    
    public static function getIndex()
    {
        return array(new self(self::GENERAL_CSS, self::TYPE_CSS));
    }
    
    public static function getStatus()
    {
        return array(
            new self(self::GENERAL_CSS, self::TYPE_CSS),
            new self(self::JQUERY, self::TYPE_JS),
            new self(self::JQUERY_UI, self::TYPE_JS),
            new self(self::JQUERY_UI_CSS, self::TYPE_CSS)
        );
    }
    
    public static function getPlayers()
    {
        return array(
            new self(self::GENERAL_CSS, self::TYPE_CSS),
            new self(self::JQUERY, self::TYPE_JS),
            new self(self::JQUERY_UI, self::TYPE_JS),
            new self(self::JQUERY_UI_CSS, self::TYPE_CSS)
        );
    }
    
    public static function getTournaments()
    {
        return array(new self(self::GENERAL_CSS, self::TYPE_CSS));
    }
    
    public static function getRankings()
    {
        return array(
            new self(self::GENERAL_CSS, self::TYPE_CSS),
            new self(self::JQUERY, self::TYPE_JS),
            new self(self::JQUERY_UI, self::TYPE_JS),
            new self(self::JQUERY_UI_CSS, self::TYPE_CSS)
        );
    }
    
    public static function getStatistics()
    {
        return array(
            new self(self::GENERAL_CSS, self::TYPE_CSS),
            new self(self::JQUERY, self::TYPE_JS),
            new self(self::JQUERY_UI, self::TYPE_JS),
            new self(self::JQUERY_UI_CSS, self::TYPE_CSS),
            new self(self::HIGHCHARTS, self::TYPE_JS),
            new self(self::HIGHCHARTS_THEME, self::TYPE_JS)
        );
    }
    
    public static function getPlayersOfTheMonth()
    {
        return array(new self(self::GENERAL_CSS, self::TYPE_CSS));
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }
}
