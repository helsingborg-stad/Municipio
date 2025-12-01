<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\FieldValue;

use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\FieldInterface;

interface RegisterFieldValueInterface
{
    /**
     * Register the field value with WordPress.
     *
     * @return void
     */
    public function register(FieldInterface $field): void;
}
