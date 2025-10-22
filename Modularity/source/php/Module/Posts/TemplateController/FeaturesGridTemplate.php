<?php

namespace Modularity\Module\Posts\TemplateController;

/**
 * Class FeaturesGridTemplate
 *
 * Template controller for rendering posts as a features grid.
 *
 * @package Modularity\Module\Posts\TemplateController
 */
class FeaturesGridTemplate extends AbstractController
{
    /**
     * FeaturesGridTemplate constructor.
     *
     * @param \Modularity\Module\Posts\Posts $module Instance of the Posts module.
     */
    public function __construct(\Modularity\Module\Posts\Posts $module)
    {
        parent::__construct($module);
    }
}
