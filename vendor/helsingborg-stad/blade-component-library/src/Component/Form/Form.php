<?php

namespace BladeComponentLibrary\Component\Form;

/**
 * Class Form
 * @package BladeComponentLibrary\Component\Form
 */
class Form extends \BladeComponentLibrary\Component\BaseController
{
    public function init()
    {
        extract($this->data);
    }
}