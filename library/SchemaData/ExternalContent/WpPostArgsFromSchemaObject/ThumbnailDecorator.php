<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\Schema\BaseType;
use Municipio\Schema\ImageObject;

/**
 * Decorator for adding thumbnail to post args.
 */
class ThumbnailDecorator implements WpPostArgsFromSchemaObjectInterface
{
    public const META_KEY = 'synced_from_external_source';

    /**
     * Constructor.
     */
    public function __construct(
        private WpPostArgsFromSchemaObjectInterface $inner
    ) {
    }

    /**
     * @inheritDoc
     */
    public function transform(BaseType $schemaObject): array
    {
        $postArgs = $this->inner->transform($schemaObject);

        $imageObjects = $this->extractImageObjects($schemaObject->getProperty('image'));
        $thumbnailId  = $this->getThumbnailId($imageObjects);

        if ($thumbnailId === null) {
            return $postArgs;
        }

        $postArgs['meta_input']                ??= [];
        $postArgs['meta_input']['_thumbnail_id'] = $thumbnailId;

        return $postArgs;
    }

    /**
     * Extracts ImageObject instances from the image property.
     *
     * @param mixed $imageProperty
     * @return ImageObject[]
     */
    private function extractImageObjects(mixed $imageProperty): array
    {
        $images = is_array($imageProperty) ? $imageProperty : [$imageProperty];
        return array_filter($images, fn($img) => $img instanceof ImageObject);
    }

    /**
     * Gets the thumbnail ID from the first ImageObject.
     *
     * @param ImageObject[] $imageObjects
     * @return string|null
     */
    private function getThumbnailId(array $imageObjects): ?string
    {
        if (empty($imageObjects)) {
            return null;
        }

        $firstImage = reset($imageObjects);
        $id         = $firstImage->getProperty('@id');
        return !empty($id) ? $id : null;
    }
}
