<?php

namespace Municipio\Controller\Purpose;

/**
 * Class Project
 * @package Municipio\Controller\Purpose
 */
class Project
{
    private $label;
    private $key;

    public function __construct()
    {

        $this->label = __('Project', 'municipio');
        $this->key = 'project';
    }
    /**
     * The localized, singular label used to describe this class in dropdowns and similar circumstances.
     *
     * @return string The label
     */
    public static function getLabel(): string
    {
        return self::$label;
    }
    /**
     * Returns the last part of the class name, in lowercase.
     *
     * @return string The name of the class without the namespace and without the word "Singular"
     */
    public static function getKey(): string
    {
        return self::$key;
    }
}
