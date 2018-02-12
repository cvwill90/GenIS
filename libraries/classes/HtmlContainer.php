<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HtmlContainer
 *
 * @author cl2811
 */

class HtmlContainer extends HtmlObject {
    private $content;
    
    public function content($container_content) {
        $this->content .= $container_content;
    }
    
    private function build_tag_ending(){
        $tag_ending = '</'. $this->tag .'>';
        return $tag_ending;
    }
    
    protected function render_html(){
        parent::render_html();
        $tag_opening = $this->html;
        $tag_ending = $this->build_tag_ending();
        
        $html_container = $tag_opening . $this->content . $tag_ending;
        $this->html = $html_container;
    }
    
    public function __construct($html_tag) {
        parent::__construct($html_tag);
    }
}