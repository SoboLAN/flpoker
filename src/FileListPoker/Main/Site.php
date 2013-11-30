<?php

namespace FileListPoker\Main;

use FileListPoker\Main\Config;
use FileListPoker\Main\Dictionary;
use FileListPoker\Main\FLPokerException;

/**
 * Main class of the site. Handles logic about language, dependencies, Google Analytics
 * and is responsible for rendering the header and the footer of the site.
 */
class Site
{
    private static $siteVersion = '1.1.8';
    
    //keys represent the list of valid pages
    //the values represent the keys used for obtaining menu strings from the dictionary
    //and also used in the templates. so they have a double role. be careful when changing them
    private static $pages = array (
        'index.php'         => 'menu_home',
        'player.php'        => 'menu_players',
        'players.php'       => 'menu_players',
        'tournament.php'    => 'menu_tournaments',
        'tournaments.php'   => 'menu_tournaments',
        'rankings.php'      => 'menu_rankings',
        'statistics.php'    => 'menu_statistics',
        'players.month.php' => 'menu_players_of_the_month'
    );
    
    private static $jQueryDependency = array (
        'index.php'         => false,
        'player.php'        => true,
        'players.php'       => false,
        'tournament.php'    => false,
        'tournaments.php'   => false,
        'rankings.php'      => true,
        'statistics.php'    => true,
        'players.month.php' => false
    );
    
    private static $highChartsDependency = array (
        'index.php'         => false,
        'player.php'        => false,
        'players.php'       => false,
        'tournament.php'    => false,
        'tournaments.php'   => false,
        'rankings.php'      => false,
        'statistics.php'    => true,
        'players.month.php' => false
    );
    
    private $lang;
    
    public function __construct()
    {
        if (! Config::getValue('online')) {
            throw new FLPokerException('The site is currently down for maintenance', FLPokerException::SITE_DOWN);
        }

        $cookieName = Config::getValue('lang_cookie_name');
    
        if (isset ($_COOKIE[$cookieName]) and Dictionary::isValidLanguage($_COOKIE[$cookieName])) {
            $this->lang = $_COOKIE[$cookieName];
        } else {
            $this->lang = Config::getValue('default_lang');
            
            $cookieDuration = Config::getValue('lang_cookie_duration');
            
            setcookie($cookieName, $this->lang, time() + $cookieDuration);
        }
    }
    
    public function getLanguage()
    {
        return $this->lang;
    }
    
    public function getWord($key)
    {
        return Dictionary::getWord($key, $this->lang);
    }
    
    public function getFullPageTemplate($page)
    {
        if (! in_array($page, array_keys(self::$pages))) {
            $message = "Site::getFullPageTemplate received an invalid page: $page";
            Logger::log($message);
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        $tpl = file_get_contents('templates/fullpage.tpl');
        
        $tpl = str_replace('{html_title}', 'FileList Poker - ' . Dictionary::getWord(self::$pages[$page], $this->lang), $tpl);
        
        $cssFiles = '<link rel="stylesheet" type="text/css" href="' . Config::getValue('path_general_css') . '" />';
        $scriptFiles = '';
        
        if (self::$jQueryDependency[$page]) {
            $scriptFiles .= '<script src="' . Config::getValue('path_jquery') . '"></script>';
            $scriptFiles .= '<script src="' . Config::getValue('path_jqueryui') . '"></script>';
            $cssFiles .= '<link rel="stylesheet" href="' . Config::getValue('path_jqueryui_css') . '" />';

            if (self::$highChartsDependency[$page]) {
                $scriptFiles .= '<script src="' . Config::getValue('path_highcharts') . '"></script>';
                $scriptFiles .= '<script src=' . Config::getValue('path_highcharts_export') . '></script>';
                $scriptFiles .= '<script src="js/highcharts/themes/dark-blue.js"></script>';
            }
        }
        
        $tpl = str_replace('{css_files}', $cssFiles, $tpl);
        $tpl = str_replace('{js_files}', $scriptFiles, $tpl);
        
        if (Config::getValue('enable_google_analytics')) {
            $tpl = str_replace('{google_analytics_script}', file_get_contents('templates/google_analytics.tpl'), $tpl);     
        } else {
            $tpl = str_replace('{google_analytics_script}', '', $tpl);
        }
        
        foreach (self::$pages as $key => $value) {
            $tpl = str_replace('{' . $value . '}', Dictionary::getWord($value, $this->lang), $tpl);
        }
        
        foreach (self::$pages as $key => $value) {
            $tpl = str_replace('{selected_' . $value . '}', ($page == $key) ? 'class="selected"' : '', $tpl);
        }

        $langPanel = '';
        
        if ($this->lang == 'ro') {
            $langPanel .= '
                <img class="active_lang" src="images/ro.gif" title="' . Dictionary::getWord('langpanel_ro', $this->lang) .'" alt="' . Dictionary::getWord('langpanel_ro', $this->lang) .'" />
            <a href="lang_switch.php?lang=en&amp;returnpage=' . $page . '">' .
                '<img src="images/us.gif" title="' . Dictionary::getWord('langpanel_en_switch', $this->lang) . '" alt="' . Dictionary::getWord('langpanel_en_switch', $this->lang) . '" />' .
            '</a>';
        } elseif ($this->lang == 'en') {
            $langPanel .= '
            <a href="lang_switch.php?lang=&amp;returnpage=' . $page . '">' .
                '<img src="images/ro.gif" title="' . Dictionary::getWord('langpanel_ro_switch', $this->lang) .'" alt="' . Dictionary::getWord('langpanel_ro_switch', $this->lang) .'" />' .
            '</a>
            <img class="active_lang" src="images/us.gif" title="' . Dictionary::getWord('langpanel_en', $this->lang) . '" alt="' . Dictionary::getWord('langpanel_en', $this->lang) . '" />
            ';
        }
        
        $tpl = str_replace('{language_panel_content}', $langPanel, $tpl);
        
        $tpl = str_replace('{title}', Dictionary::getWord(self::$pages[$page], $this->lang), $tpl);
        
        $tpl = str_replace('{version}', self::$siteVersion, $tpl);
        
        return $tpl;
    }
    
    public function isValidID($id)
    {
        return (strlen($id) <= 4 and ctype_digit($id));
    }
    
    
    
    
    
    
    
    //one of the very few functions that contain template information (the other one is getFooter)
    //not a good practice, but I made an exception this time
    public function getHeader ($page = 'index.php')
    {
        $pageTitle = '';
        switch($page)
        {
            case 'index.php':          $pageTitle = Dictionary::getWord('menu_home', $this->lang); break;
            case 'players.php':        $pageTitle = Dictionary::getWord('menu_players', $this->lang); break;
            case 'tournaments.php':    $pageTitle = Dictionary::getWord('menu_tournaments', $this->lang); break;
            case 'rankings.php':       $pageTitle = Dictionary::getWord('menu_rankings', $this->lang); break;    
            case 'statistics.php':     $pageTitle = Dictionary::getWord('menu_statistics', $this->lang); break;
            case 'players.month.php':  $pageTitle = Dictionary::getWord('menu_players_of_the_month', $this->lang); break;
            case 'contact.php':        $pageTitle = Dictionary::getWord('menu_contact', $this->lang); break;
            default:
                $message = "Site::getHeader received an invalid page: $page";
                Logger::log($message);
                throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        $out = '<!DOCTYPE html>
        <html>
        <head>
        <meta charset="utf-8">
        <title>FileList Poker Points - ' . $pageTitle . ' (JavaFling)</title>
        <link rel="stylesheet" type="text/css" href="' . Config::getValue('path_general_css') . '" />
        <link rel="shortcut icon" href="favicon.ico" />';
        
        if (self::$jQueryDependency[$page]) {
            $out .= '<script src="' . Config::getValue('path_jquery') . '"></script>';
            $out .= '<script src="' . Config::getValue('path_jqueryui') . '"></script>';
            $out .= '<link rel="stylesheet" href="' . Config::getValue('path_jqueryui_css') . '" />';

            if (self::$highChartsDependency[$page]) {
                $out .= '<script src="' . Config::getValue('path_highcharts') . '"></script>';
                $out .= '<script src=' . Config::getValue('path_highcharts_export') . '></script>';
                $out .= '<script src="js/highcharts/themes/dark-blue.js"></script>';
            }
        }
        
        
        $out .= '</head><body>';
        
        if (Config::getValue('enable_google_analytics')) {
            $out .= $this->getAnalyticsScript();
        }
        
        $out .= '<div id="container">
            <div id="logo"><img src="images/logofl.png" /></div>
            <ul class="claybricks">';
        
        $out .= '<li><a href="index.php" ' . (($page == 'index.php') ? 'class="selected">' : '>') .
                Dictionary::getWord('menu_home', $this->lang) . '</a></li>';
        
        $out .= '<li><a href="players.php" ' . (($page == 'players.php') ? 'class="selected">' : '>') .
                Dictionary::getWord('menu_players', $this->lang) . '</a></li>';
        
        $out .= '<li><a href="tournaments.php" ' . (($page == 'tournaments.php') ? 'class="selected">' : '>') .
                Dictionary::getWord('menu_tournaments', $this->lang) . '</a></li>';
        
        $out .= '<li><a href="rankings.php" ' . (($page == 'rankings.php') ? 'class="selected">' : '>') .
                Dictionary::getWord('menu_rankings', $this->lang) . '</a></li>';
        
        $out .= '<li><a href="statistics.php" ' . (($page == 'statistics.php') ? 'class="selected">' : '>') .
                Dictionary::getWord('menu_statistics', $this->lang) . '</a></li>';
        
        $out .= '<li><a href="players.month.php" ' . (($page == 'players.month.php') ? 'class="selected">' : '>') .
                Dictionary::getWord('menu_players_of_the_month', $this->lang) . '</a></li>';

        $out .= '</ul>
        <div id="language_panel">';
        
        if ($this->lang == 'ro') {
            $out .= '
                <img class="active_lang" src="images/ro.gif" title="' . Dictionary::getWord('langpanel_ro', $this->lang) .'" alt="' . Dictionary::getWord('langpanel_ro', $this->lang) .'" />
            <a href="lang_switch.php?lang=en&amp;returnpage=' . $page . '">' .
                '<img src="images/us.gif" title="' . Dictionary::getWord('langpanel_en_switch', $this->lang) . '" alt="' . Dictionary::getWord('langpanel_en_switch', $this->lang) . '" />' .
            '</a>';
        } elseif ($this->lang == 'en') {
            $out .= '
            <a href="lang_switch.php?lang=&amp;returnpage=' . $page . '">' .
                '<img src="images/ro.gif" title="' . Dictionary::getWord('langpanel_ro_switch', $this->lang) .'" alt="' . Dictionary::getWord('langpanel_ro_switch', $this->lang) .'" />' .
            '</a>
                <img class="active_lang" src="images/us.gif" title="' . Dictionary::getWord('langpanel_en', $this->lang) . '" alt="' . Dictionary::getWord('langpanel_en', $this->lang) . '" />
            ';
        }
        
        $out .= '</div>';
        
        return $out;
    }
    
    public function getFooter ()
    {
        $out = '<div id="footer">
            FileList Poker v1.1.8 (currently in feature freeze).
            <br />
            Copyright &copy; 2013 Radu Murzea.
            <br />
            Project Author and Site Design: Radu Murzea.
        </div>';
        
        $out .= '</div>'; //closing id="container" too
        
        return $out;
    }
}
