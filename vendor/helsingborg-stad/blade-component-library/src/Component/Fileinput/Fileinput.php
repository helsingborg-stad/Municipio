<?php

namespace BladeComponentLibrary\Component\Fileinput;

class Fileinput extends \BladeComponentLibrary\Component\BaseController
{
    public function init()
    {
        //Extract array for eazy access (fetch only)
        extract($this->data);

        if ($display === 'area') {
            $this->data['classList'][] = 'c-fileinput--area';
        }
    }
}
