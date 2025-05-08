<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

class MultilineStringField extends AbstractField implements FieldInterface
{
    public function toArray(): array
    {
        return [
            // 'type'  => 'textarea',
            'type'  => 'textarea',
            'name'  => $this->name,
            'key'   => $this->getKey(),
            'label' => $this->label
        ];
    }

    public function sanitizeValue(mixed $value = null): mixed
    {
        return is_string($value) ? $value : '';
    }
}
