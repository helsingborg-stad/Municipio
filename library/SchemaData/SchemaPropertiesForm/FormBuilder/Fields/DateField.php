<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

use DateTimeInterface;

/**
 * Class DateField
 */
class DateField extends AbstractField implements FieldInterface
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type'  => 'date_picker',
            'key'   => $this->getKey(),
            'name'  => $this->getName(),
            'label' => $this->getLabel(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getValue(): mixed
    {
        if ($this->value instanceof DateTimeInterface) {
            return $this->value->format('Y-m-d');
        }

        return is_string($this->value) ? $this->value : null;
    }
}
