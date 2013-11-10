<?php

namespace FileListPoker\Main;

use FileListPoker\Main\Config;

/**
 * Main class of the site. Handles logic about language, dependencies, Google Analytics
 * and is responsible for rendering the header and the footer of the site.
 */
class Site
{
    private static $availableLangs = array('en', 'ro');
    
    private static $jQueryDependency = array (
        'index.php'            => false,
        'players.php'        => true,
        'tournaments.php'    => false,
        'rankings.php'        => true,
        'statistics.php'    => true,
        'players.month.php' => false,
        'contact.php'        => false,
    );
    
    private static $highChartsDependency = array (
        'index.php'            => false,
        'players.php'        => false,
        'tournaments.php'    => false,
        'rankings.php'        => false,
        'statistics.php'    => true,
        'players.month.php' => false,
        'contact.php'        => false,
    );
    
    private $lang;
    private $wording;
    
    public function __construct ()
    {
        if (! Config::getValue('online')) {
            die ('The site is currently down for maintenance.');
        }

        $this->fillLanguage();
        $this->fillWording($this->lang);
    }
    
    private function fillWording ($language)
    {
        require_once 'wording/text.' . $language . '.php';

        $this->wording = $siteLabels;
    }
    
    private function fillLanguage ()
    {
        $cookiename = Config::getValue('lang_cookie_name');
    
        if (isset ($_COOKIE[$cookiename])) {
            $userlang = $_COOKIE[$cookiename];
            
            if (in_array($userlang, self::$availableLangs)) {
                $this->lang = $userlang;
            }
        } else {
            $this->lang = Config::getValue('default_lang');
        }
    }
    
    public function getLanguage ()
    {
        return $this->lang;
    }
    
    public function getWord ($key)
    {
        return $this->wording[$key];
    }
    
    //one of the very few functions that contain template information (the other one is getFooter)
    //not a good practice, but I made an exception this time
    public function getHeader ($page = 'index.php')
    {
        $pageTitle = '';
        switch($page)
        {
            case 'index.php':            $pageTitle = $this->wording['menu_home']; break;
            case 'players.php':            $pageTitle = $this->wording['menu_players']; break;
            case 'tournaments.php':        $pageTitle = $this->wording['menu_tournaments']; break;
            case 'rankings.php':        $pageTitle = $this->wording['menu_rankings']; break;    
            case 'statistics.php':        $pageTitle = $this->wording['menu_statistics']; break;
            case 'players.month.php':    $pageTitle = $this->wording['menu_players_of_the_month']; break;
            case 'contact.php':            $pageTitle = $this->wording['menu_contact']; break;
            default: die('Invalid Page');
        }
        
        $out = '<!DOCTYPE html>
        <html>
        <head>
        <meta charset="utf-8">
        <title>FileList Poker Points - ' . $pageTitle . ' (JavaFling)</title>
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <link rel="shortcut icon" href="favicon.ico" />';
        
        if (self::$jQueryDependency[$page]) {
            $out .= '<script src="js/jquery-1.10.2.min.js"></script>';
            $out .= '<script src="js/jquery-ui-1.10.3.custom.min.js"></script>';
            $out .= '<link rel="stylesheet" href="css/dark-hive/jquery-ui-1.10.3.custom.min.css" />';

            if (self::$highChartsDependency[$page]) {
                $out .= '<script src="js/highcharts/highcharts.js"></script>';
                $out .= '<script src="js/highcharts/modules/exporting.js"></script>';
                $out .= '<script src="js/highcharts/themes/dark-blue.js"></script>';
            }
        }
        
        
        $out .= '</head><body>';
        
        if (Config::getValue('enable_google_analytics')) {
            $out .= $this->getAnalyticsScript();
        }
        
        $out .= '<div id="container">
            <ul class="claybricks">';
        
        $out .= '<li><a href="index.php" ' . (($page == 'index.php') ? 'class="selected">' : '>') .
                $this->wording['menu_home'] . '</a></li>';
        
        $out .= '<li><a href="players.php" ' . (($page == 'players.php') ? 'class="selected">' : '>') .
                $this->wording['menu_players'] . '</a></li>';
        
        $out .= '<li><a href="tournaments.php" ' . (($page == 'tournaments.php') ? 'class="selected">' : '>') .
                $this->wording['menu_tournaments'] . '</a></li>';
        
        $out .= '<li><a href="rankings.php" ' . (($page == 'rankings.php') ? 'class="selected">' : '>') .
                $this->wording['menu_rankings'] . '</a></li>';
        
        $out .= '<li><a href="statistics.php" ' . (($page == 'statistics.php') ? 'class="selected">' : '>') .
                $this->wording['menu_statistics'] . '</a></li>';
        
        $out .= '<li><a href="players.month.php" ' . (($page == 'players.month.php') ? 'class="selected">' : '>') .
                $this->wording['menu_players_of_the_month'] . '</a></li>';

        $out .= '</ul>
        <p id="language_panel">';
        
        if ($this->lang == 'ro') {
            $out .= '
                <img class="active_lang" src="images/ro.gif" title="' . $this->wording['langpanel_ro'] .'" alt="' . $this->wording['langpanel_ro'] .'" />
            <a href="lang_switch.php?lang=en&amp;returnpage=' . $page . '">
                <img src="images/us.gif" title="' . $this->wording['langpanel_en_switch'] . '" alt="' . $this->wording['langpanel_en_switch'] . '" />
            </a>';
        } elseif ($this->lang == 'en') {
            $out .= '
            <a href="lang_switch.php?lang=&amp;returnpage=' . $page . '">
                <img src="images/ro.gif" title="' . $this->wording['langpanel_ro_switch'] .'" alt="' . $this->wording['langpanel_ro_switch'] .'" />
            </a>
                <img class="active_lang" src="images/us.gif" title="' . $this->wording['langpanel_en'] . '" alt="' . $this->wording['langpanel_en'] . '" />
            ';
        }
            
        $out .= '</p>';
        
        return $out;
    }

    public function getFooter ()
    {
        $out = '<div id="footer">
            FileList Poker Points v1.0.14.
            <br />
            Copyright &copy; 2013 Radu Murzea.
            <br />
            Project Author and Site Design: Radu Murzea.
            <div id="contact-button">
                    <a href="contact.php"><img alt="Contact" src="images/contact-button.jpg" /></a>
                </div>
        </div>';
        
        $out .= '</div>'; //closing id="container" too
        
        return $out;
    }
    
    private function getAnalyticsScript ()
    {
        $out = "<script>
            (function (i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

            ga('create', 'UA-40294384-2', 'javafling.org');
            ga('send', 'pageview');
        </script>";
        
        return $out;
    }
}
