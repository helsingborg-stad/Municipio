<?php

namespace BladeComponentLibrary\Component\Logotype;

class Logotype extends \BladeComponentLibrary\Component\BaseController
{

    public function init() {

        //Extract array for eazy access (fetch only)
        extract($this->data);

        //Add placeholder class
        if(!$src) {
            $this->data['classList'][] = $this->getBaseClass() . "--is-placeholder";
        }

        //Inherit the alt text
        if(!$alt && $caption) {
            $this->data['alt'] = $this->data['caption'];
        }

        //Has ripple
        if($hasRipple) {
            $this->data['classList'][] = "ripple"; 
            $this->data['classList'][] = "ripple--before"; 
        }
    }
}
