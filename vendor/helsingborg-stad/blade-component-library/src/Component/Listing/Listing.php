<?php

namespace BladeComponentLibrary\Component\Listing;

class Listing extends \BladeComponentLibrary\Component\BaseController  
{
    
    public function init() {
        //Extract array for eazy access (fetch only)
        extract($this->data);
    }
}