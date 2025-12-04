<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

/**
 * Class TabField
 *
 * This class is responsible for creating a tab field.
 */
class TabField extends AbstractField implements FieldInterface
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type'      => 'tab',
            'name'      => $this->getName(),
            'key'       => $this->getKey(),
            'label'     => $this->getLabel(),
            'placement' => 'left'
        ];
    }
}
