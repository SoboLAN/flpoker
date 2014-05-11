<?php

namespace FileListPoker\Renderers;

/**
 * @author Radu Murzea <radu.murzea@gmail.com>
 */
class Paginator
{
    private $total;
    private $pageLength;
    private $page;
    private $width;
    private $maxPage;
    private $start;
    private $end;
    
    /**
     * Constructor. The parameters provided to this function must respect the following rules:
     * <ul>
     * <li>All parameters must be positive integers</li>
     * <li>Pagination width must be an odd integer strictly greater than 1 (e.g. 3, 5, 7, 9 etc.)</li>
     * <li>The current page must be one that has some elements (at least 1). How you
     * determine this is your problem.</li>
     * </ul>
     * The class will not enforce these rules. If you do not respect them, the output will be
     * unpredictable and probably incorrect. Use at your own risk.
     * @param int $totalElements how many elements are in total (not per page).
     * @param int $pageLength how many of the elements must be displayed in a page.
     * @param int $currentPage the requested page.
     * @param int $paginationWidth how many page links will be displayed.
     */
    public function __construct($totalElements, $pageLength, $currentPage, $paginationWidth)
    {
        $this->total = $totalElements;
        $this->pageLength = $pageLength;
        $this->page = $currentPage;
        $this->width = $paginationWidth;
        
        $this->maxPage = intval(ceil($totalElements / $pageLength));
        
        $this->calculateBoundaries();
    }
    
    /**
     * Returns the pagination.
     * @return array an array containing the same number of elements as the value of $paginationWidth
     * with 2 possible extra elements. A response may contain a "prev" link and / or a "next" link if
     * there is a previous or a next page, respectively.
     * A typical response will look like this:
     * array(
     *  0 => array('page' => 3, 'type' => 'prev'),
     *  1 => array('page' => 2, 'type' => 'normal'),
     *  2 => array('page' => 3, 'type' => 'normal'),
     *  3 => array('page' => 4, 'type' => 'current'),
     *  4 => array('page' => 5, 'type' => 'normal'),
     *  5 => array('page' => 6, 'type' => 'normal'),
     *  6 => array('page' => 5, 'type' => 'next')
     * );
     * When displayed, the above result will look like this:
     * 
     * Previous -- 2 -- 3 -- 4 -- 5 -- 6 -- Next
     * 
     * As you can see, the current page (4) is in the center.
     */
    public function getPagination()
    {        
        $elements = array();
        $index = 0;
        
        if ($this->page > 1) {
            $elements[$index++] = array('page' => $this->page - 1, 'type' => 'prev');
        }
        
        for ($i = $this->start; $i <= $this->end; $i++) {
            $type = ($i == $this->page) ? 'current' : 'normal';
            $elements[$index++] = array('page' => $i, 'type' => $type);
        }
        
        if ($this->page < $this->maxPage) {
            $elements[$index] = array('page' => $this->page + 1, 'type' => 'next');
        }
        
        return $elements;
    }
    
    private function calculateBoundaries()
    {
        $halfLength = ($this->width - 1) / 2;
        
        //based on all parameters, figure out where page display starts and where it ends.
        //do it on paper to convince yourself that it's correct :)
        //first case is the normal one: the first page and the last page are sufficiently far
        //apart that it allows the current page to fall on the central pivot
        if ($this->page - $halfLength >= 1 && $this->page + $halfLength <= $this->maxPage) {
            $this->start = $this->page - $halfLength;
            $this->end = $this->page + $halfLength;
        //the second case is when the current page is among the first pages, sufficiently
        //close to 1 that it causes it to slide to the left
        } elseif ($this->page - $halfLength < 1) {
            $this->start = 1;
            $this->end = min($this->width, $this->maxPage);
        //the last case is when the current page is among the last pages, sufficiently
        //close to maximum that it causes it to slide to the right
        } elseif ($this->page + $halfLength > $this->maxPage) {
            $this->start = max($this->maxPage - $this->width + 1, 1);
            $this->end = $this->maxPage;
        }
    }
}