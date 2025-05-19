<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

/**
 * Class WysiwygField
 *
 * This class is responsible for creating a WYSIWYG field.
 */
class WysiwygField extends AbstractField implements FieldInterface
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type'         => 'wysiwyg',
            'name'         => $this->getName(),
            'key'          => $this->getKey(),
            'label'        => $this->getLabel(),
            'media_upload' => false,
            'toolbar'      => 'full',
        ];
    }
}
