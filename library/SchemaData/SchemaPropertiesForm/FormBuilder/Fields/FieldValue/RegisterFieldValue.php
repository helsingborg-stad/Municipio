<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\FieldValue;

use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\FieldInterface;
use WpService\Contracts\AddFilter;

class RegisterFieldValue implements RegisterFieldValueInterface
{
    public function __construct(private AddFilter $wpService)
    {
    }

    public function register(FieldInterface $field): void
    {
        $this->wpService->addFilter("acf/load_value/key={$field->getKey()}", fn () => $field->getValue(), 10);
    }
}
