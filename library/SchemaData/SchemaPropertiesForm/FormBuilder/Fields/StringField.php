<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

/**
 * Class StringField
 *
 * This class is responsible for creating a string field.
 */
class StringField extends AbstractField implements FieldInterface
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type' => 'text',
            'name' => $this->getName(),
            'key' => $this->getKey(),
            'label' => $this->getLabel(),
            'instructions' => $this->getInstructions(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getValue(): mixed
    {
        return is_string($this->value) ? $this->value : '';
    }
}
