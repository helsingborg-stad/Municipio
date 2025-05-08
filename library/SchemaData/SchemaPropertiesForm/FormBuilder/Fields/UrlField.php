<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

class UrlField extends AbstractField implements FieldInterface
{
    public function toArray(): array
    {
        return [
            'type'  => 'url',
            'key'   => $this->getKey(),
            'name'  => $this->name,
            'label' => $this->label,
        ];
    }

    public function sanitizeValue(mixed $value = null): mixed
    {
        if (is_string($value) && filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return '';
    }
}
