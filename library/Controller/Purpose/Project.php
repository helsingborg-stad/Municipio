<?php

namespace Municipio\Controller\Purpose;

/**
 * Class Project
 * @package Municipio\Controller\Purpose
 */
class Project extends PurposeFactory
{
    private $label;
    private $key;

    public function __construct()
    {
        $this->label = __('Project', 'municipio');
        $this->key = 'project';
    }
    public function getLabel(): string
    {
        return $this->label;
    }
    public function getKey(): string
    {
        return $this->key;
    }
}
