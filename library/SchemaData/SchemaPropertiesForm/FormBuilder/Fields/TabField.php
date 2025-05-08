<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

class TabField extends AbstractField implements FieldInterface
{
    public function toArray(): array
    {
        return [
            'type'      => 'tab',
            'name'      => $this->name,
            'key'       => $this->getKey(),
            'label'     => $this->label,
            'placement' => 'left'
        ];
    }

    public function sanitizeValue(mixed $value = null): mixed
    {
        return is_string($value) ? $value : '';
    }
}
