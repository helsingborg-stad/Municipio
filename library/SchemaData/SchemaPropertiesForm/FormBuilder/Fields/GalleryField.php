<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

use Municipio\Schema\ImageObject;

class GalleryField extends AbstractField implements FieldInterface
{
    public function toArray(): array
    {
        return [
            'type'          => 'gallery',
            'name'          => $this->name,
            'key'           => $this->getKey(),
            'label'         => $this->label,
            'return_format' => 'array',
        ];
    }

    public function getValue(): mixed
    {
        if (!is_array($this->value)) {
            return [];
        }

        // If is array of ImageObject
        $imageObjects = array_filter($this->value, function ($item) {
            return $item instanceof ImageObject;
        });

        // Return only image ids
        return array_map(function ($item) {
            return $item['@id'];
        }, $imageObjects);
    }
}
