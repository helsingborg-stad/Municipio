<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

use DateTime;

/**
 * Class DateTimeField
 *
 * This class is responsible for creating a date and time field.
 */
class DateTimeField extends AbstractField implements FieldInterface
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type'           => 'date_time_picker',
            'key'            => $this->getKey(),
            'name'           => $this->getName(),
            'label'          => $this->getLabel(),
            'format'         => 'Y-m-d H:i:s',
            'display_format' => 'Y-m-d H:i:s',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getValue(): mixed
    {
        if (is_a($this->value, DateTime::class)) {
            return $this->value->format('Y-m-d H:i:s');
        }

        return is_string($this->value) ? $this->value : '';
    }
}
