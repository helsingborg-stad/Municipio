<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

/**
 * Class WysiwygField
 *
 * This class is responsible for creating a WYSIWYG field.
 */
class UrlField extends AbstractField implements FieldInterface
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type'  => 'url',
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
        return is_string($this->value) && filter_var($this->value, FILTER_VALIDATE_URL) ? $this->value : null;
    }
}
