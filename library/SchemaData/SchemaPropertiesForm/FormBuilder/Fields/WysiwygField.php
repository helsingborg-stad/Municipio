<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

class WysiwygField extends AbstractField implements FieldInterface
{
    public function toArray(): array
    {
        return [
            // 'type'  => 'textarea',
            'type'         => 'wysiwyg',
            'name'         => $this->name,
            'key'          => $this->getKey(),
            'label'        => $this->label,
            'media_upload' => false,
            'toolbar'      => 'full',
        ];
    }

    public function sanitizeValue(mixed $value = null): mixed
    {
        return $valye;
    }
}
