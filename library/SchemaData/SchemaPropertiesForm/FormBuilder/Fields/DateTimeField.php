<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

use DateTime;

class DateTimeField extends AbstractField implements FieldInterface
{
    public function toArray(): array
    {
        return [
            'type'           => 'date_time_picker',
            'key'            => $this->getKey(),
            'name'           => $this->name,
            'label'          => $this->label,
            'format'         => 'Y-m-d H:i:s',
            'display_format' => 'Y-m-d H:i:s',
        ];
    }

    public function getValue(): mixed
    {
        if (is_a($this->value, DateTime::class)) {
            return $this->value->format('Y-m-d H:i:s');
        }

        return is_string($this->value) ? $this->value : '';
    }
}
