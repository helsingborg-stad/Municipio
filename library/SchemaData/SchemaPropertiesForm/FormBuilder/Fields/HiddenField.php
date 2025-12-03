<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

/**
 * Class HiddenField
 *
 * This class is responsible for creating a hidden field.
 */
class HiddenField extends AbstractField
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type'    => 'text',
            'name'    => $this->getName(),
            'key'     => $this->getKey(),
            'label'   => $this->getLabel(),
            'value'   => $this->getValue(),
            'wrapper' => [
                'class' => 'hidden',
            ],
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
