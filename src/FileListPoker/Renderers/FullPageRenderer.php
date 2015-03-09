<?php

namespace FileListPoker\Renderers;

use FileListPoker\Renderers\GeneralRenderer;
use FileListPoker\Main\Site;
use FileListPoker\Main\Dictionary;
use FileListPoker\Main\Config;
use FileListPoker\Main\Logger;

/**
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
class FullPageRenderer extends GeneralRenderer
{
    private $site;
    
    //keys represent the list of valid pages
    //the values represent the keys used for obtaining menu strings from the dictionary
    //and also used in the templates. so they have a double role. be careful when changing them
    private $pages = array (
        'index.php'         => 'menu_home',
        'status.php'        => 'menu_status',
        'players.php'       => 'menu_players',
        'tournaments.php'   => 'menu_tournaments',
        'rankings.php'      => 'menu_rankings',
        'statistics.php'    => 'menu_statistics',
        'players.month.php' => 'menu_players_of_the_month'
    );
    
    public function __construct(Site $site)
    {
        $this->site = $site;
    }
    
    /**
     * Returns the site's main template with the most important blocks already filled in. This content
     * of those blocks depends on the specified page (the title, whether or not jQuery is included etc.)
     * @param string $page the page you're currently on.
     * @return string the main template.
     */
    public function renderPage($page)
    {
        if (! in_array($page, array_keys($this->pages))) {
            $message = "Site::getFullPageTemplate received an invalid page: $page";
            Logger::log($message);
            throw new FLPokerException($message, FLPokerException::ERROR);
        }
        
        $tpl = file_get_contents('templates/fullpage.tpl');
        
        $this->fillTitle($tpl, $page);
        $this->fillJavascriptFiles($tpl, $page);
        $this->fillCSSFiles($tpl, $page);
        $this->fillGoogleAnalyticsScript($tpl);
        $this->fillMenuItems($tpl, $page);
        $this->fillLanguagePanel($tpl, $page);
        $this->fillVersion($tpl);
        
        return $tpl;
    }
    
    private function fillTitle(&$tpl, $forPage)
    {
        $tpl = str_replace(
            array('{html_title}', '{title}'),
            'FileList Poker - ' . Dictionary::getWord($this->pages[$forPage], $this->site->getLanguage()),
            $tpl
        );
    }
    
    private function fillJavascriptFiles(&$tpl, $forPage)
    {
        //transform file name into method that needs to be called in order to determine that file's dependencies
        //for example: we determine that on index.php we need to call Dependency::getIndex()
        $dependencyMethod = Dependency::$pages[$forPage];
        $dependencies = Dependency::$dependencyMethod();
        
        $scriptFiles = '';
        foreach ($dependencies as $dependency) {
            if ($dependency->getType() == Dependency::TYPE_JS) {
                $scriptFiles .= str_replace('{place}', Config::getValue($dependency->getName()), Dependency::TYPE_JS);
            }
        }
        
        $tpl = str_replace('{js_files}', $scriptFiles, $tpl);
    }
    
    private function fillCSSFiles(&$tpl, $forPage)
    {
        //transform file name into method that needs to be called in order to determine that file's dependencies
        //for example: we determine that on index.php we need to call Dependency::getIndex()
        $dependencyMethod = Dependency::$pages[$forPage];
        $dependencies = Dependency::$dependencyMethod();
        
        $cssFiles = '';
        foreach ($dependencies as $dependency) {
            if ($dependency->getType() == Dependency::TYPE_CSS) {
                $cssFiles .= str_replace('{place}', Config::getValue($dependency->getName()), Dependency::TYPE_CSS);
            }
        }
        
        $tpl = str_replace('{css_files}', $cssFiles, $tpl);
    }
    
    private function fillGoogleAnalyticsScript(&$tpl)
    {
        $analyticsScript = Config::getValue('enable_google_analytics') ?
                            file_get_contents('templates/google_analytics.tpl') :
                            '';
        
        $tpl = str_replace('{google_analytics_script}', $analyticsScript, $tpl);
    }
    
    private function fillMenuItems(&$tpl, $forPage)
    {
        foreach ($this->pages as $fileName => $menuName) {
            $translatedMenuName = Dictionary::getWord($menuName, $this->site->getLanguage());
            $selectedString = ($forPage == $fileName) ? 'class="selected"' : '';
            
            $tpl = str_replace(
                array('{' . $menuName . '}', '{selected_' . $menuName . '}'),
                array($translatedMenuName, $selectedString),
                $tpl
            );
        }
    }
    
    private function fillLanguagePanel(&$tpl, $forPage)
    {
        $langPanel = '';
        $language = $this->site->getLanguage();
        
        if ($language == 'ro') {
            $langPanel .= '
                <img class="active_lang" src="images/ro.gif" title="' . Dictionary::getWord('langpanel_ro', $language) .'" alt="' . Dictionary::getWord('langpanel_ro', $language) .'" />
            <a href="lang_switch.php?lang=en&amp;returnpage=' . $forPage . '">' .
                '<img src="images/us.gif" title="' . Dictionary::getWord('langpanel_en_switch', $language) . '" alt="' . Dictionary::getWord('langpanel_en_switch', $language) . '" />' .
            '</a>';
        } elseif ($language == 'en') {
            $langPanel .= '
            <a href="lang_switch.php?lang=&amp;returnpage=' . $forPage . '">' .
                '<img src="images/ro.gif" title="' . Dictionary::getWord('langpanel_ro_switch', $language) .'" alt="' . Dictionary::getWord('langpanel_ro_switch', $language) .'" />' .
            '</a>
            <img class="active_lang" src="images/us.gif" title="' . Dictionary::getWord('langpanel_en', $language) . '" alt="' . Dictionary::getWord('langpanel_en', $language) . '" />
            ';
        }
        
        $tpl = str_replace('{language_panel_content}', $langPanel, $tpl);
    }
    
    private function fillVersion(&$tpl)
    {
        $tpl = str_replace('{version}', Config::getValue('site_version'), $tpl);
    }
}
