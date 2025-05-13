<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

use DateTime;

class TimeField extends AbstractField implements FieldInterface
{
    public function toArray(): array
    {
        return [
            'type'           => 'time_picker',
            'key'            => $this->getKey(),
            'name'           => $this->name,
            'label'          => $this->label,
            'display_format' => 'H:i',
        ];
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
