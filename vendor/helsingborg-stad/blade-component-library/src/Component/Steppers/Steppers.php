<?php

namespace BladeComponentLibrary\Component\Steppers;

class Steppers extends \BladeComponentLibrary\Component\BaseController
{
    public function init() {
        //Extract array for eazy access (fetch only)
        extract($this->data);

        //Create padding modifier
        if($type) {
            $this->data['classList'][] = $this->getBaseClass() . "--type-" . $type;
        }
    }
}