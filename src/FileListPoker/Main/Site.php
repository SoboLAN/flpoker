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
    private static $jQueryDependency = array (
        'index.php'         => false,
        'players.php'       => true,
        'tournaments.php'   => false,
        'rankings.php'      => true,
        'statistics.php'    => true,
        'players.month.php' => false
    );
    
    private static $highChartsDependency = array (
        'index.php'         => false,
        'players.php'       => false,
        'tournaments.php'   => false,
        'rankings.php'      => false,
        'statistics.php'    => true,
        'players.month.php' => false
    );
    
    private $lang;
    
    public function __construct ()
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
    
    public function getLanguage ()
    {
        return $this->lang;
    }
    
    public function getWord ($key)
    {
        return Dictionary::getWord($key, $this->lang);
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
