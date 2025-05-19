<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

use Municipio\Schema\ImageObject;

/**
 * Class GalleryField
 *
 * This class is responsible for creating a gallery field.
 */
class GalleryField extends AbstractField implements FieldInterface
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type'          => 'gallery',
            'name'          => $this->getName(),
            'key'           => $this->getKey(),
            'label'         => $this->getLabel(),
            'return_format' => 'array',
        ];
    }

    /**
     * @inheritDoc
     */
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
