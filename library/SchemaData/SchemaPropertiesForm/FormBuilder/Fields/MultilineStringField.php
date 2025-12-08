<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

/**
 * Class MultilineStringField
 *
 * This class is responsible for creating a multiline string field.
 */
class MultilineStringField extends AbstractField implements FieldInterface
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            // 'type'  => 'textarea',
            'type'  => 'textarea',
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
        return is_string($this->value) ? $this->value : '';
    }
}
