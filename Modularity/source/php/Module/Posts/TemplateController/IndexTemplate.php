<?php

namespace Modularity\Module\Posts\TemplateController;

/**
 * Class IndexTemplate
 *
 * Template controller for rendering posts as an index.
 *
 * @package Modularity\Module\Posts\TemplateController
 */
class IndexTemplate extends AbstractController
{
    /**
     * IndexTemplate constructor.
     *
     * @param \Modularity\Module\Posts\Posts $module Instance of the Posts module.
     */
    public function __construct(\Modularity\Module\Posts\Posts $module)
    {
        parent::__construct($module);
    }
}
