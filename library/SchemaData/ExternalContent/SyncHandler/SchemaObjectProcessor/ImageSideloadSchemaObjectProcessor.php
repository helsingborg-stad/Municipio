<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\SchemaObjectProcessor;

use WP_Error;
use Municipio\Schema\{BaseType, ImageObject, Schema};
use WpService\Contracts\{IsWpError, MediaSideloadImage, UpdatePostMeta, WpGetAttachmentUrl, WpUpdatePost};

/**
 * Processes the 'image' property of schema objects by sideloading images and updating references.
 */
class ImageSideloadSchemaObjectProcessor implements SchemaObjectProcessorInterface
{
    private const META_KEY_MEDIA_HASH = '_media_hash';


    /**
     * Constructor
     */
    public function __construct(
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
        if (empty($images = $this->normalizeImages($schemaObject->getProperty('image')))) {
            return $schemaObject;
        }

        $imageObjects = array_map(fn($img) => $this->processImage($img), $images);
        $imageObjects = array_filter($imageObjects);

        return $schemaObject->setProperty('image', $imageObjects);
    }

    /**
     * Normalize image property to an array of image data.
     *
     * @param mixed $images
     * @return array
     */
    private function normalizeImages($images): array
    {
        if (empty($images)) {
            return [];
        }
        return is_array($images) ? $images : [$images];
    }

    /**
     * Process a single image, sideloading and returning media info.
     *
     * @param mixed $img
     * @return ImageObject|null
     */
    private function processImage($img): ?ImageObject
    {
        $imageData = $this->extractImageData($img);

        if (empty($imageData['url'])) {
            return null;
        }

        $mediaId = $this->sideloadImage(
            $imageData['name'],
            $imageData['url'],
            $imageData['alt'],
            $imageData['caption']
        );

        if ($this->wpService->isWpError($mediaId)) {
            return null;
        }

        return Schema::imageObject()
            ->identifier($mediaId)
            ->name($imageData['name'])
            ->url($this->wpService->wpGetAttachmentUrl($mediaId))
            ->description($imageData['alt'])
            ->caption($imageData['caption']);
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
     * Sideload an image from a URL and set alt/caption.
     *
     * @param string|null $title
     * @param string|null $url
     * @param string|null $alt
     * @param string|null $caption
     * @return int|WP_Error Attachment ID or error
     */
    protected function sideloadImage(?string $title = '', ?string $url = '', ?string $alt = '', ?string $caption = ''): int|WP_Error
    {
        $this->loadSideloadDependencies();

        $mediaHash = $this->getMediaHash($title, $url, $alt, $caption);
        $mediaId   = $this->getImageIdFromPreviousSideload($mediaHash);

        if (is_null($mediaId)) {
            $mediaId = $this->wpService->mediaSideloadImage($url, 0, $caption, 'id');
        }

        if ($this->wpService->isWpError($mediaId)) {
            return $mediaId;
        }

        if ($alt) {
            $this->wpService->updatePostMeta($mediaId, '_wp_attachment_image_alt', $alt);
        }

        if ($title) {
            $this->wpService->wpUpdatePost([
                'ID'         => $mediaId,
                'post_title' => $title,
                'meta_input' => [
                    self::META_KEY_MEDIA_HASH => $mediaHash
                ]
            ]);
        }

        return $mediaId;
    }

    private function getImageIdFromPreviousSideload(string $mediaHash): ?int
    {
        // get post by media hash on post meta
        $posts = get_posts([
            'meta_key'    => self::META_KEY_MEDIA_HASH,
            'meta_value'  => $mediaHash,
            'post_type'   => 'attachment',
            'numberposts' => 1
        ]);

        return !empty($posts) ? (int)$posts[0]->ID : null;
    }

    private function getMediaHash(?string $title = '', ?string $url = '', ?string $alt = '', ?string $caption = ''): string
    {
        return md5($url . $title . $alt . $caption);
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
