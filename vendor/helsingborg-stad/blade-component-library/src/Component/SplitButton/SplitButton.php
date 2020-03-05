<?php

namespace BladeComponentLibrary\Component\Splitbutton;

class SplitButton extends \BladeComponentLibrary\Component\BaseController
{

    public function init() {
        //Extract array for eazy access (fetch only)
        extract($this->data);
    }
}
