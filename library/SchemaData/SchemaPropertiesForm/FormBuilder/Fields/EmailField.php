<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

class EmailField extends AbstractField implements FieldInterface
{
    public function toArray(): array
    {
        return [
            'type'  => 'email',
            'name'  => $this->name,
            'key'   => $this->getKey(),
            'label' => $this->label
        ];
    }

    public function sanitizeValue(mixed $value = null): mixed
    {
        return filter_var($value, FILTER_SANITIZE_EMAIL) ?: '';
    }
}
