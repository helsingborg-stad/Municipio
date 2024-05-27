<?php

namespace Municipio\ExternalContent\WpPostMetaFactory;

use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\ImageObject;
use WpService\Contracts\GetPosts;
use WpService\Contracts\MediaSideloadImage;

class WpPostMetaFactoryThumbnailDecorator implements WpPostMetaFactoryInterface
{
    public function __construct(private WpPostMetaFactoryInterface $inner, private MediaSideloadImage&GetPosts $wpService)
    {
    }

    public function create(BaseType $schemaObject): array
    {
        $meta     = $this->inner->create($schemaObject);
        $imageUrl = $this->getImageUrl($schemaObject);


        if (!empty($imageUrl)) {
            $attachmentId = $this->getAttachmentBySourceUrl($imageUrl);

            if (empty($attachmentId)) {
                $attachmentId = $this->wpService->mediaSideloadImage($imageUrl, 0, null, 'id');
            }

            $meta['_thumbnail_id'] = $attachmentId;
        }

        return $meta;
    }

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
