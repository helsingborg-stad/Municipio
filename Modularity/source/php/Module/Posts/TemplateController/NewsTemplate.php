<?php

namespace Modularity\Module\Posts\TemplateController;

/**
 * Class NewsTemplate
 *
 * Template controller for rendering posts as an index.
 *
 * @package Modularity\Module\Posts\TemplateController
 */
class NewsTemplate extends AbstractController
{
    /**
     * NewsTemplate constructor.
     *
     * @param \Modularity\Module\Posts\Posts $module Instance of the Posts module.
     */
    public function __construct(\Modularity\Module\Posts\Posts $module)
    {
        parent::__construct($module);
    }
}
