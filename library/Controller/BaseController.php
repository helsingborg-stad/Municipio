<?php

namespace Municipio\Controller;

class BaseController
{
    /**
     * Holds the view's data
     * @var array
     */
    protected $data = array();

    public function __construct()
    {
        add_filter('HbgBlade/data', array($this, 'getData'));
        $this->init();
    }

    public function init()
    {
        // Method body
    }

    /**
     * Returns the data
     * @return array Data
     */
    public function getData()
    {
        return $this->data;
    }
}
