<?php

namespace Municipio\Controller\Purpose;

abstract class PurposeFactory
{
    private $key;
    private $label;

    public function __construct($key, $label)
    {
        $this->key = $key;
        $this->label = $label;
    }
    public function getLabel()
    {
        return $this->label;
    }

    public function getKey()
    {
        return $this->key;
    }
}
