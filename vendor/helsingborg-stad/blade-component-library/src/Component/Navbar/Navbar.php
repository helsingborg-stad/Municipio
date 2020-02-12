<?php

namespace BladeComponentLibrary\Component\Navbar;

/**
 * Class Card
 * @package BladeComponentLibrary\Component\Navbar
 */
class Navbar extends \BladeComponentLibrary\Component\BaseController
{
    public function init() {

        // Extract array for eazy access (fetch only)
        extract($this->data);
    }
}
