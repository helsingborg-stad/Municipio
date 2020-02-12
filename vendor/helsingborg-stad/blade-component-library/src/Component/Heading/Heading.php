<?php

namespace BladeComponentLibrary\Component\Heading;

class Heading extends \BladeComponentLibrary\Component\BaseController  
{
    
    public function init() {
        //Extract array for eazy access (fetch only)
        extract($this->data);

        $this->data['classList'][] = "heading--size-" . $level; 
    }
}