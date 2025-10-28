<?php

namespace Modularity\Module\Posts\TemplateController;

/**
 * Class CollectionTemplate
 *
 * Template controller for rendering a collection of posts.
 *
 * @package Modularity\Module\Posts\TemplateController
 */
class CollectionTemplate extends AbstractController
{
    protected $module;

    /**
     * CollectionTemplate constructor.
     *
     * @param \Modularity\Module\Posts\Posts $module Instance of the Posts module.
     */
    public function __construct(\Modularity\Module\Posts\Posts $module)
    {
        parent::__construct($module);
    }
}
