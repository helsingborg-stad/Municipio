<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\SchemaObjectProcessor;

use WP_Error;
use Municipio\Schema\{BaseType, ImageObject, Schema};
use Municipio\SchemaData\ExternalContent\SyncHandler\LocalImageObjectIdGenerator\LocalImageObjectIdGeneratorInterface;
use WpService\Contracts\{IsWpError, MediaSideloadImage, UpdatePostMeta, WpGetAttachmentUrl, WpUpdatePost};

/**
 * Processes the 'image' property of schema objects by sideloading images and updating references.
 */
class ImageSideloadSchemaObjectProcessor implements SchemaObjectProcessorInterface
{
    public const META_KEY_IMAGE_ID         = '_external_image_id';
    public const META_KEY_SCHEMA_PARENT_ID = '_schema_parent_id';

    /**
     * Constructor
     */
    public function __construct(
        private MediaSideloadImage&UpdatePostMeta&IsWpError&WpGetAttachmentUrl&WpUpdatePost $wpService
    ) {
    }

    /**
     * Process a schema object, sideloading images and updating references recursively.
     *
     * @param BaseType $schemaObject
     * @return BaseType
     */
    public function process(BaseType $schemaObject): BaseType
    {
        return $this->processImagesRecursively($schemaObject, $schemaObject);
    }

    /**
     * Recursively process all properties for ImageObject instances.
     *
     * @param mixed $value
     * @return mixed
     */
    private function processImagesRecursively(BaseType $schemaObject, mixed $value)
    {
        if (is_array($value)) {
            return array_map(fn($item) => $this->processImagesRecursively($schemaObject, $item), $value);
        }

        if ($value instanceof ImageObject) {
            $processed = $this->processImage($schemaObject, $value);
            return $processed ?? $value;
        }

        if ($value instanceof BaseType) {
            foreach ($value->getProperties() as $key => $prop) {
                $processedProp = $this->processImagesRecursively($schemaObject, $prop);
                $value         = $value->setProperty($key, $processedProp);
            }
            return $value;
        }

        return $value;
    }

    /**
     * Process a single image, sideloading and returning media info.
     *
     * @param BaseType|null $schemaObject
     * @param ImageObject $imageObject
     * @return ImageObject|null
     */
    private function processImage(?BaseType $schemaObject, ImageObject $imageObject): ?ImageObject
    {
        $imageObject->sameAs($imageObject->getProperty('url')); // Used to create identifier.
        $mediaId = $this->sideloadImage($schemaObject ?? $imageObject, $imageObject);

        if ($this->wpService->isWpError($mediaId)) {
            return null;
        }

        // Update image object with media info
        return $imageObject
            ->setProperty('@id', $mediaId)
            ->url($this->wpService->wpGetAttachmentUrl($mediaId));
    }

    /**
     * Sideload an image from a URL and set alt/caption.
     *
     * @param ImageObject $imageObject
     * @return int|WP_Error Attachment ID or error
     */
    protected function sideloadImage(BaseType $schemaObject, ImageObject $imageObject): int|WP_Error
    {
        $this->loadSideloadDependencies();

        $mediaId = $this->getImageIdFromPreviousSideload($schemaObject, $imageObject);

        if (!is_null($mediaId)) {
            return $mediaId;
        }

        if (empty($imageObject->getProperty('url'))) {
            return new WP_Error('no_image_url', 'No image URL provided for sideloading.');
        }

        if (is_null($mediaId)) {
            $mediaId = $this->wpService->mediaSideloadImage($imageObject->getProperty('url'), 0, $imageObject->getProperty('caption'), 'id');
        }

        if ($this->wpService->isWpError($mediaId)) {
            return $mediaId;
        }

        $this->wpService->updatePostMeta($mediaId, self::META_KEY_IMAGE_ID, $imageObject->getProperty('sameAs'));

        $this->wpService->wpUpdatePost([
            'ID'         => $mediaId,
            'post_title' => $imageObject->getProperty('name') ?? '',
            'meta_input' => [
                self::META_KEY_IMAGE_ID         => $imageObject->getProperty('sameAs'),
                '_wp_attachment_image_alt'      => $imageObject->getProperty('description') ?? '',
                self::META_KEY_SCHEMA_PARENT_ID => $schemaObject->getProperty('@id') ?? 0,
            ]
        ]);

        return $mediaId;
    }

    /**
     * Get the image ID from a previous sideload based on the image URL.
     *
     * @param BaseType $schemaObject
     * @param ImageObject $imageObject
     * @return int|null
     */
    private function getImageIdFromPreviousSideload(BaseType $schemaObject, ImageObject $imageObject): ?int
    {
        // get post by media hash on post meta
        $posts = get_posts([
            'meta_query'  => [
                [
                    'key'     => self::META_KEY_IMAGE_ID,
                    'value'   => $imageObject->getProperty('sameAs'),
                    'compare' => '=',
                ],
                [
                    'key'     => self::META_KEY_SCHEMA_PARENT_ID,
                    'value'   => $schemaObject->getProperty('@id') ?? 0,
                    'compare' => '=',
                ],
            ],
            'post_type'   => 'attachment',
            'numberposts' => 1
        ]);

        return !empty($posts) ? (int)$posts[0]->ID : null;
    }

    /**
     * Load WordPress dependencies for sideloading images.
     */
    private function loadSideloadDependencies(): void
    {
        $files = [
            ABSPATH . 'wp-admin/includes/image.php',
            ABSPATH . 'wp-admin/includes/file.php',
            ABSPATH . 'wp-admin/includes/media.php',
        ];

        foreach ($files as $file) {
            if (!file_exists($file)) {
                return;
            }

            require_once $file;
        }
    }
}
