<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\ImageObject;
use WpService\Contracts\GetPosts;
use WpService\Contracts\MediaSideloadImage;

/**
 * Decorator for adding thumbnail to post args.
 */
class ThumbnailDecorator implements WpPostArgsFromSchemaObjectInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private WpPostArgsFromSchemaObjectInterface $inner,
        private MediaSideloadImage&GetPosts $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function create(BaseType $schemaObject, SourceInterface $source): array
    {
        $post     = $this->inner->create($schemaObject, $source);
        $imageUrl = $this->getImageUrl($schemaObject);

        if (!empty($imageUrl)) {
            $attachmentId = $this->getAttachmentBySourceUrl($imageUrl);

            if (empty($attachmentId)) {
                $attachmentId = $this->wpService->mediaSideloadImage($imageUrl, 0, null, 'id');
            }

            if (empty($attachmentId) || $attachmentId instanceof \WP_Error) {
                return $post;
            }

            $post['meta_input']['_thumbnail_id'] = $attachmentId;
        }

        return $post;
    }

    /**
     * Get attachment by source url.
     *
     * @param string $sourceUrl
     * @return int|null
     */
    private function getAttachmentBySourceUrl(string $sourceUrl): ?int
    {
        $attachment = $this->wpService->getPosts([
            'post_type'      => 'attachment',
            'posts_per_page' => 1,
            'meta_key'       => '_source_url',
            'meta_value'     => $sourceUrl,
        ]);

        return empty($attachment) ? null : $attachment[0]->ID;
    }

    /**
     * Get image url from schema object.
     *
     * @param BaseType $schemaObject
     * @return string|null
     */
    private function getImageUrl(BaseType $schemaObject): ?string
    {
        $image = $schemaObject->getProperty('image');

        if (empty($image)) {
            return null;
        }

        if (is_string($image) && filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        if ($image instanceof ImageObject && filter_var($image->getProperty('url'), FILTER_VALIDATE_URL)) {
            return $image->getProperty('url');
        }

        return null;
    }
}
