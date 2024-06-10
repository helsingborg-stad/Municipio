<?php

namespace Municipio\Controller\ContentType\Complex;

use Municipio\Controller\ContentType\Complex\School\SchoolDataPreparer;
use Municipio\Controller\ContentType;
use Municipio\Helper\WP;

/**
 * Class School
 * @package Municipio\Controller\ContentType
 */
class School extends ContentType\ContentTypeFactory
{
    protected object $postMeta;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->key   = 'School';
        $this->label = __('School', 'municipio');

        parent::__construct($this->key, $this->label);
    }

    /**
     * Add hooks for the School content type.
     *
     * @return void
     */
    public function addHooks(): void
    {
        $dataPreparer = new SchoolDataPreparer();

        add_filter('Municipio/viewData', [$dataPreparer, 'prepareData'], 10, 1);
    }
}
