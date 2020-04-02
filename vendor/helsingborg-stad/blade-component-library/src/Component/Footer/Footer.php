<?php

namespace BladeComponentLibrary\Component\Footer;

class Footer extends \BladeComponentLibrary\Component\BaseController  
{
    public function init() {
        //Extract array for eazy access (fetch only)
        extract($this->data);

        foreach($links as $link)
        {
            if(!array_key_exists('target', $link) && array_key_exists('href', $link))
            {
                $link['target'] = ' ';
            }
        }

    }
}