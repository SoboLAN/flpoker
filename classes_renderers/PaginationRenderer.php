<?php

namespace FileListPoker\Renderers;

use FileListPoker\Main\Site;

/**
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
class PaginationRenderer
{
    private $site;
    
    public function __construct(Site $site)
    {
        $this->site = $site;
    }
    
    public function render($blockTpl, $elementTpl, $content, $file)
    {
        $elements = array();
        for ($i = 0; $i < count($content); $i++) {
            
            switch($content[$i]['type']) {
                case 'normal':
                    $current = '';
                    $title = $this->site->getWord('pagination_normal_title');
                    $page = $content[$i]['page'];
                    $text = $content[$i]['page'];
                    break;
                    
                case 'current':
                    $current = 'class="current"';
                    $title = $this->site->getWord('pagination_current_title');
                    $page = $content[$i]['page'];
                    $text = $content[$i]['page'];
                    break;
                    
                case 'prev':
                    $current = '';
                    $title = $this->site->getWord('pagination_prev_title');
                    $page = $content[$i]['page'];
                    $text = $this->site->getWord('pagination_prev_text');
                    break;
                    
                case 'next':
                    $current = '';
                    $title = $this->site->getWord('pagination_next_title');
                    $page = $content[$i]['page'];
                    $text = $this->site->getWord('pagination_next_text');
                    break;
            }
            
            $elements[$i] = str_replace(
                array('{current}', '{title}', '{file}', '{page}', '{text}'),
                array($current, $title, $file, $page, $text),
                $elementTpl
            );
        }
        
        return str_replace('{elements}', implode("\n", $elements), $blockTpl);
    }
}