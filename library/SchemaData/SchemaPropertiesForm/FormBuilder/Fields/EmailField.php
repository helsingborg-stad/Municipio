<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

/**
 * Class EmailField
 *
 * This class is responsible for creating an email field.
 */
class EmailField extends AbstractField implements FieldInterface
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type'  => 'email',
            'name'  => $this->getName(),
            'key'   => $this->getKey(),
            'label' => $this->getLabel()
        ];
    }

    /**
     * @inheritDoc
     */
    public function getValue(): mixed
    {
        return filter_var($this->value, FILTER_VALIDATE_EMAIL) ? $this->value : '';
    }
}
