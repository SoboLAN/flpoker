<?php

namespace FileListPoker\Renderers;

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
    
    public function render($blockTpl, $elementTpl, $content)
    {
        
    }
}