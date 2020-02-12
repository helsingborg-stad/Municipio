<?php

namespace BladeComponentLibrary\Component\Pagination;

class Pagination extends \BladeComponentLibrary\Component\BaseController  
{
    
    public function init() {
        //Extract array for eazy access (fetch only)
        extract($this->data);

        //Default to page one
        if(!$current) {
            $this->data['current'] = 1; 
        }

        //Previous data
        if($this->data['current'] != 1) {
            $this->data['previous'] = $linkPrefix . ($this->data['current'] - 1); 
        } else {
            $this->data['previous'] = false; 
        }

        //Next data
        if((count($this->data['list'])) != $this->data['current']) {
            $this->data['next'] = $linkPrefix . ($this->data['current'] + 1); 
        } else {
            $this->data['next'] = false; 
        }
    }
}