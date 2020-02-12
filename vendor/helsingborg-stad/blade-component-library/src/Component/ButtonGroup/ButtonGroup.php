<?php

namespace BladeComponentLibrary\Component\ButtonGroup;

/**
 * Class ButtonGroup
 * @package BladeComponentLibrary\Component\ButtonGroup
 */
class ButtonGroup extends \BladeComponentLibrary\Component\BaseController
{
    public function init() {
      
        //Extract array for eazy access (fetch only)
        extract($this->data);

        if(isset($toggle) && $toggle){
            $this->data['container'] = 'js-toggle-container';
        }

        if(isset($borderColor)){
            $this->data['classList'][] = $this->getBaseClass() . '__border--' . $borderColor; 
        }

        if(isset($backgroundColor)){
            $this->data['classList'][] = $this->getBaseClass() . '--' . $backgroundColor; 
        }
       
    }
}