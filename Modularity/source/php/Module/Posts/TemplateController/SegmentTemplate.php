<?php

namespace Modularity\Module\Posts\TemplateController;

/**
 * Class SegmentTemplate
 *
 * Template controller for rendering posts as Segment.
 *
 * @package Modularity\Module\Posts\TemplateController
 * 
 */
class SegmentTemplate extends AbstractController
{
    /**
     * The instance of the Posts module associated with this template.
     *
     * @var \Modularity\Module\Posts\Posts
     */
    protected $module;
    protected $args;
    public $data;
    public $fields;

    /**
     * SegmentTemplate constructor.
     *
     * @param \Modularity\Module\Posts\Posts $module Instance of the Posts module.
     */
    public function __construct(\Modularity\Module\Posts\Posts $module)
    {
        parent::__construct($module);
    }
}
