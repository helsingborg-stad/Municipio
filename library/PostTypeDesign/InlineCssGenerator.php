<?php

namespace Municipio\PostTypeDesign;

class InlineCssGenerator 
{
    public function __construct(private array $designConfig, private array $fields)
    {}
    
    public function generate() 
    {
        if (empty($this->designConfig) || empty($this->fields)) {
            return '';
        }

        foreach ($this->fields as $field) {
            if (empty($designConfig[$field['settings']]) || empty($field['output'])) {
                continue;
            }
        }
    }
}