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
    public const META_KEY_IMAGE_ID = '_external_image_id';

    /**
     * Constructor
     */
    public function __construct(
        private LocalImageObjectIdGeneratorInterface $localImageIdGenerator,
        private MediaSideloadImage&UpdatePostMeta&IsWpError&WpGetAttachmentUrl&WpUpdatePost $wpService
    ) {
    }

    /**
     * Process a schema object, sideloading images and updating references.
     *
     * @param BaseType $schemaObject
     * @return BaseType
     */
    public function process(BaseType $schemaObject): BaseType
    {
        if (empty($imageObjects = $this->normalizeImages($schemaObject->getProperty('image')))) {
            return $schemaObject;
        }

        $imageObjects = array_map(fn($img) => $this->processImage($schemaObject, $img), $imageObjects);
        $imageObjects = array_filter($imageObjects);

        return $schemaObject->setProperty('image', $imageObjects);
    }

    /**
     * Normalize image property to an array of ImageObject.
     *
     * @param mixed $images
     * @return ImageObject[]
     */
    private function normalizeImages($images): array
    {
        if (empty($images)) {
            return [];
        }

        $imageObjects = array_map(fn($img) => $this->extractImageData($img), is_array($images) ? $images : [$images]);

        return array_filter($imageObjects, fn($img) => !empty($img->getProperty('url')));
    }

    /**
     * Extract image data from string or ImageObject.
     *
     * @param mixed $img
     * @return ImageObject
     */
    private function extractImageData($img): ImageObject
    {
        if (is_a($img, ImageObject::class)) {
            return $img;
        }

        if (is_string($img)) {
            Schema::imageObject()->url($img);
        }

        return Schema::imageObject();
    }

    /**
     * Process a single image, sideloading and returning media info.
     *
     * @param mixed $img
     * @return ImageObject|null
     */
    private function processImage(BaseType $schemaObject, ImageObject $imageObject): ?ImageObject
    {
        $imageObject->sameAs($imageObject->getProperty('url')); // Used to create identifier.
        $mediaId = $this->sideloadImage($schemaObject, $imageObject);

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

        $localImageId = $this->localImageIdGenerator->generateId($schemaObject, $imageObject);
        $mediaId      = $this->getImageIdFromPreviousSideload($localImageId);

        if (is_null($mediaId)) {
            $mediaId = $this->wpService->mediaSideloadImage($imageObject->getProperty('url'), 0, $imageObject->getProperty('caption'), 'id');
        }

        if ($this->wpService->isWpError($mediaId)) {
            return $mediaId;
        }

        if ($imageObject->getProperty('description')) {
            $this->wpService->updatePostMeta($mediaId, '_wp_attachment_image_alt', $imageObject->getProperty('description'));
        }

        $this->wpService->wpUpdatePost([
            'ID'         => $mediaId,
            'post_title' => $imageObject->getProperty('name') ?? '',
            'meta_input' => [
                self::META_KEY_IMAGE_ID => $localImageId,
            ]
        ]);

        return $mediaId;
    }

    /**
     * Get the image ID from a previous sideload based on the image URL.
     *
     * @param string $url
     * @return int|null
     */
    private function getImageIdFromPreviousSideload(string $url): ?int
    {
        // get post by media hash on post meta
        $posts = get_posts([
            'meta_key'    => self::META_KEY_IMAGE_ID,
            'meta_value'  => $url,
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
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
    }
}
