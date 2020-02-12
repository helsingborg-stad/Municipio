<?php

namespace BladeComponentLibrary\Component\Accordion;

/**
 * Class Accordion
 * @package BladeComponentLibrary\Component\Accordion
 */
class Accordion extends \BladeComponentLibrary\Component\BaseController
{
    public function init() {
        //Extract array for eazy access (fetch only)
        extract($this->data);

        $this->data['id'] = uniqid();
    }
}
