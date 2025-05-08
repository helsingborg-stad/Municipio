<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

class BooleanField extends AbstractField implements FieldInterface
{
    public function toArray(): array
    {
        return [
            'type'  => 'true_false',
            'name'  => $this->name,
            'key'   => $this->getKey(),
            'label' => $this->label
        ];
    }

    public function sanitizeValue(mixed $value = null): mixed
    {
        return is_bool($value) ? $value : false;
    }
}
