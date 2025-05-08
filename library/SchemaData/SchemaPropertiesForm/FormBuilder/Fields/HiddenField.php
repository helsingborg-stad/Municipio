<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

class HiddenField extends AbstractField
{
    public function toArray(): array
    {
        return [
            'type'    => 'text',
            'name'    => $this->getName(),
            'key'     => $this->getKey(),
            'label'   => $this->getLabel(),
            'wrapper' => [
                'class' => 'hidden',
            ],
        ];
    }

    public function getValue(): mixed
    {
        return is_string($this->value) ? $this->value : '';
    }
}
