<?php

namespace Municipio\Template;

class CoreBase
{
    private $template;

    /**
     * Initializes a template
     * @param string $template The template to use
     */
    public function __construct($template)
    {
        $this->template = $template;
        $this->init();
    }

    public function init()
    {
        return false;
    }
}
