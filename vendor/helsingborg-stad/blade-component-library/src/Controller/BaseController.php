<?php

namespace BladeComponentLibrary\Controller;

class BaseController
{
    /**
     * Holds the view's data
     * @var array
     */
    protected $data = array();

    /**
     * Run init
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Returns the data
     * 
     * @return array Data
     */
    public function getData()
    {
        return (array) $this->data; 
    }
}