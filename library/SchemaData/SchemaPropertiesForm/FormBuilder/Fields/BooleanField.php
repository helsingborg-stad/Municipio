<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

/**
 * Class BooleanField
 */
class BooleanField extends AbstractField implements FieldInterface
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type'  => 'true_false',
            'name'  => $this->getName(),
            'key'   => $this->getKey(),
            'label' => $this->getLabel(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getValue(): mixed
    {
        return is_bool($this->value) ? $this->value : false;
    }
}
