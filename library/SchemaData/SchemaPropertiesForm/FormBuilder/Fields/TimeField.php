<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

/**
 * Class TimeField
 *
 * This class is responsible for creating a time field.
 */
class TimeField extends AbstractField implements FieldInterface
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type'           => 'time_picker',
            'key'            => $this->getKey(),
            'name'           => $this->getName(),
            'label'          => $this->getName(),
            'display_format' => 'H:i',
        ];
    }
}
