<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

class DateField extends AbstractField implements FieldInterface
{
    public function toArray(): array
    {
        return [
            'type'  => 'date_picker',
            'key'   => $this->getKey(),
            'name'  => $this->name,
            'label' => $this->label,
        ];
    }

    public function getValue(): mixed
    {
        return is_string($this->value) ? $this->value : null;
    }
}
