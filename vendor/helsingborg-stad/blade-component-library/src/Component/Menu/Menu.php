<?php

namespace BladeComponentLibrary\Component\Menu;

class Menu extends \BladeComponentLibrary\Component\BaseController  
{
    
    public function init() {

        //Extract array for eazy access (fetch only)
        extract($this->data);
        
        //Horizontal menu
        if($isHorizontal) {
            $this->data['classList'][] = $this->getBaseClass() . "--horizontal"; 
        } else {
            $this->data['classList'][] = $this->getBaseClass() . "--vertical";
        }
    }
}