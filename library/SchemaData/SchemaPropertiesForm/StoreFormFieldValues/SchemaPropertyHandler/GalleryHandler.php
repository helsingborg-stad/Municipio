<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use WpService\WpService;

/**
 * Handles the storage of gallery field values for schema data.
 *
 * This class implements the SchemaPropertyHandlerInterface and is responsible
 * for handling gallery fields in the schema data.
 */
class GalleryHandler implements SchemaPropertyHandlerInterface
{
    /**
     * Constructor.
     */
    public function __construct(private WpService $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function supports(string $propertyName, string $fieldType, mixed $value, array $propertyTypes): bool
    {
        return
            $fieldType === 'gallery'
            && is_array($value)
            && in_array('ImageObject[]', $propertyTypes)
            && !empty(array_filter($value, 'is_numeric'));
    }

    /**
     * @inheritDoc
     */
    public function handle(BaseType $schemaObject, string $propertyName, mixed $value): BaseType
    {
        $imageIds = array_filter($value, 'is_numeric');

        if (empty($imageIds)) {
            return $schemaObject;
        }

        return $schemaObject->setProperty($propertyName, array_map(
            function ($imageId) {
                return Schema::imageObject()
                    ->identifier($imageId)
                    ->name($this->wpService->getPost($imageId)->post_title)
                    ->url($this->wpService->wpGetAttachmentImageUrl($imageId, 'full'))
                    ->caption($this->wpService->wpGetAttachmentCaption($imageId))
                    ->description($this->wpService->getPostMeta($imageId, '_wp_attachment_image_alt', true));
            },
            $imageIds
        ));
    }
}
