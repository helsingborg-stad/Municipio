<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\Schema\BaseType;
use Municipio\Schema\ImageObject;
use Municipio\SchemaData\ExternalContent\SyncHandler\LocalImageObjectIdGenerator\LocalImageObjectIdGeneratorInterface;

/**
 * Decorator for adding thumbnail to post args.
 */
class ConnectUploadedImagesToPost implements WpPostArgsFromSchemaObjectInterface
{
    public const META_KEY = '_local_image_objects';

    /**
     * Constructor.
     */
    public function __construct(
        private LocalImageObjectIdGeneratorInterface $localImageIdGenerator,
        private WpPostArgsFromSchemaObjectInterface $inner
    ) {
    }

    /**
     * @inheritDoc
     */
    public function transform(BaseType $schemaObject): array
    {
        $postArgs     = $this->inner->transform($schemaObject);
        $imageObjects = $this->extractImageObjects($schemaObject->getProperty('image'));

        if (empty($imageObjects)) {
            return $postArgs;
        }

        $localImageObjectIds = array_map(fn($imageObject) => $this->localImageIdGenerator->generateId($schemaObject, $imageObject), $imageObjects);

        $postArgs['meta_input']                       ??= [];
        $postArgs['meta_input']['_local_image_objects'] = $localImageObjectIds;

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
}
