<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

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
        return is_string($this->value) ? $this->value : null;
    }
}
