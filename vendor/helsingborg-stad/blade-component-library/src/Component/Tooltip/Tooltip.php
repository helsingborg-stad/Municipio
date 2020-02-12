<?php

namespace BladeComponentLibrary\Component\Tooltip;

class Tooltip extends \BladeComponentLibrary\Component\BaseController
{
    public function init() {
        //Extract array for eazy access (fetch only)
        extract($this->data);
        
        $this->data['isLink'] = $this->data['componentElement'] === 'a';
    }
}
