<?php

namespace BladeComponentLibrary\Component\Alert;

/**
 * Class Alert
 * @package BladeComponentLibrary\Component\Alert
 */
class Alert extends \BladeComponentLibrary\Component\BaseController  
{
    public function init() {

        //Extract array for eazy access (fetch only)
        extract($this->data);

        //Overlay
        $this->data['classList'][] = $this->getBaseClass() . "--overlay-" . $overlay; 
    }
}