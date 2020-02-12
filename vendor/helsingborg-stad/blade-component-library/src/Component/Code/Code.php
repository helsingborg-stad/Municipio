<?php

namespace BladeComponentLibrary\Component\Code;

class Code extends \BladeComponentLibrary\Component\BaseController  
{
    
    public function init() {

        //Extract array for eazy access (fetch only)
        extract($this->data);

        //Escape
        if($escape) {
            $this->data['slot'] = htmlentities($slot); 
        }

        $language = ($language) ? $language : 'php';

    }
}