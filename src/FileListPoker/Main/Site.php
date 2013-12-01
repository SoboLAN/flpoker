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
    private static $siteVersion = '1.1.13';
    
    //keys represent the list of valid pages
    //the values represent the keys used for obtaining menu strings from the dictionary
    //and also used in the templates. so they have a double role. be careful when changing them
    private static $pages = array (
        'index.php'         => 'menu_home',
        'players.php'       => 'menu_players',
        'tournaments.php'   => 'menu_tournaments',
        'rankings.php'      => 'menu_rankings',
        'statistics.php'    => 'menu_statistics',
        'players.month.php' => 'menu_players_of_the_month'
    );
    
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
}
