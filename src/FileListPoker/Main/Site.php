<?php

namespace FileListPoker\Main;

use FileListPoker\Main\Config;
use FileListPoker\Main\Dictionary;
use FileListPoker\Main\FLPokerException;
use FileListPoker\Main\Logger;

/**
 * Main class of the site. Handles dependencies, Google Analytics
 * and is responsible for rendering the common blocks of the site
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
class Site
{
    //current version of the site
    private static $siteVersion = '1.3.7';
    
    //keys represent the list of valid pages
    //the values represent the keys used for obtaining menu strings from the dictionary
    //and also used in the templates. so they have a double role. be careful when changing them
    private static $pages = array (
        'index.php'         => 'menu_home',
        'status.php'        => 'menu_status',
        'players.php'       => 'menu_players',
        'tournaments.php'   => 'menu_tournaments',
        'rankings.php'      => 'menu_rankings',
        'statistics.php'    => 'menu_statistics',
        'players.month.php' => 'menu_players_of_the_month'
    );
    
    //specifies which pages must have jQuery AND jQueryUI as dependency
    private static $jQueryDependency = array (
        'index.php'         => false,
        'status.php'        => true,
        'players.php'       => true,
        'tournaments.php'   => false,
        'rankings.php'      => true,
        'statistics.php'    => true,
        'players.month.php' => false
    );
    
    //specifies which pages must have HighCharts as dependency
    private static $highChartsDependency = array (
        'index.php'         => false,
        'status.php'        => false,
        'players.php'       => false,
        'tournaments.php'   => false,
        'rankings.php'      => false,
        'statistics.php'    => true,
        'players.month.php' => false
    );
    
    //current language of the site
    private $lang;
    
    public function __construct()
    {
        if (! Config::getValue('online')) {
            throw new FLPokerException('The site is currently down for maintenance', FLPokerException::SITE_OFFLINE);
        }

        $cookieName = Config::getValue('lang_cookie_name');
    
        //read language from the cookie or use default language and write it to the cookie to be used
        //on the next request
        if (isset ($_COOKIE[$cookieName]) and Dictionary::isValidLanguage($_COOKIE[$cookieName])) {
            $this->lang = $_COOKIE[$cookieName];
        } else {
            $this->lang = Config::getValue('default_lang');
            
            $cookieDuration = Config::getValue('lang_cookie_duration');
            
            setcookie($cookieName, $this->lang, time() + $cookieDuration);
        }
    }
    
    /**
     * Get the language the site is currently using.
     * @return string a valid language
     */
    public function getLanguage()
    {
        return $this->lang;
    }
    
    /**
     * Get the word specified by the key in the site's current language.
     * @param string $key the word key
     * @return mixed a string representing the desired word or null if it doesn't exist.
     */
    public function getWord($key)
    {
        return Dictionary::getWord($key, $this->lang);
    }
    
    /**
     * Returns the site's main template with the most important blocks already filled in. This content
     * of those blocks depends on the specified page (the title, whether or not jQuery is included etc.)
     * @param string $page the page you're currently on.
     * @return string the main template.
     */
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
                $scriptFiles .= '<script src="js/highcharts/themes/dark-green.js"></script>';
            }
        }
        
        $analyticsScript = Config::getValue('enable_google_analytics') ?
                            file_get_contents('templates/google_analytics.tpl') :
                            '';
        
        $tpl = str_replace(
            array('{css_files}', '{js_files}', '{google_analytics_script}'),
            array($cssFiles, $scriptFiles, $analyticsScript),
            $tpl
        );
        
        foreach (self::$pages as $key => $value) {
            $tpl = str_replace(
                array('{' . $value . '}', '{selected_' . $value . '}'),
                array(Dictionary::getWord($value, $this->lang), ($page == $key) ? 'class="selected"' : ''),
                $tpl
            );
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
        
        $tpl = str_replace(
            array('{language_panel_content}', '{title}', '{version}'),
            array($langPanel, Dictionary::getWord(self::$pages[$page], $this->lang), self::$siteVersion),
            $tpl
        );
        
        return $tpl;
    }
    
    /**
     * Utility function that tells you whether the specified ID is valid.
     * A valid ID is made up only of digits, maximum 4 of them.
     * @param int $id the ID.
     * @return bool true if the specified ID is valid, false otherwise.
     */
    public function isValidID($id)
    {
        return (strlen($id) <= 4 and ctype_digit($id));
    }
}
