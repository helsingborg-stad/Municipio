<?php

namespace BladeComponentLibrary\Component\Footer;

class Footer extends \BladeComponentLibrary\Component\BaseController  
{
    public function init() {
        //Extract array for eazy access (fetch only)
        extract($this->data);
    }
}