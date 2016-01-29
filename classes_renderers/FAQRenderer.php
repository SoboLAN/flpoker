<?php

namespace FileListPoker\Renderers;

class FAQRenderer extends GeneralRenderer
{
    public function render($content, $mainTpl, $elementTpl)
    {
        $result = '';
        
        foreach ($content as $elementTitle => $elementContent) {
            $result .= str_replace(
                array('{acc_element_title}', '{acc_element_content}'),
                array($elementTitle, $this->renderElement($elementContent)),
                $elementTpl
            );
        }
        
        return str_replace('{accordion_elements}', $result, $mainTpl);
    }
    
    private function renderElement($elementContent)
    {
        $result = '';
        $i = 0;
        
        while (isset($elementContent[$i])) {
            switch($elementContent[$i]['type']) {
                case 'row':
                    $result .= $this->renderRow($elementContent[$i]['content']);
                    break;
                case 'table':
                    $result .= $this->renderTable($elementContent[$i]['titles'], $elementContent[$i]['rows']);
                    break;
            }
            
            //if there will be a "next" element, then put some space after the current one; looks prettier
            if (isset($elementContent[$i + 1])) {
                $result .= '<br /><br />';
            }
            
            $i++;
        }
        
        return $result;
    }
    
    private function renderRow($row)
    {
        return $row;
    }
    
    private function renderTable($titles, $rows)
    {
        $result = '<table class="presentation-table" style="width: 90%; margin: 0 auto">';
        
        $result .= '<tr>';
        foreach ($titles as $title) {
            $result .= '<th><strong>' . $title . '</strong></th>';
        }
        $result .= '</tr>';
        
        foreach ($rows as $row) {
            
            $result .= '<tr>';
            foreach ($row as $cell) {
                $result .= '<td>' . $cell . '</td>';
            }
            $result .= '</tr>';
        }
        
        $result .= '</table>';
        
        return $result;
    }
}
