<?php

namespace Municipio\MirroredPost;

use Municipio\MirroredPost\PostObject\MirroredPostObject;
use Municipio\MirroredPost\Utils\GetOtherBlogId\GetOtherBlogIdInterface;
use Municipio\PostObject\Factory\CreatePostObjectFromWpPost;
use Municipio\PostObject\PostObjectInterface;
use WpService\WpService;

/**
 * Enables the Mirrored Post feature in WordPress.
 */
class MirroredPostFeature
{
    /**
     * Constructor.
     *
     * @param WpService $wpService The WordPress service instance.
     */
    public function __construct(private GetOtherBlogIdInterface $getOtherBlogId, private WpService $wpService)
    {
    }

    /**
     * Enable the Mirrored Post feature.
     *
     * This method sets up the necessary hooks and filters to enable the Mirrored Post functionality.
     */
    public function enable(): void
    {
        $this->decoratePostObject();
    }

    /**
     * Decorate the post object.
     *
     * This method sets up a filter to decorate the post object with mirrored post functionality.
     */
    private function decoratePostObject(): void
    {
        $this->wpService->addFilter(
            CreatePostObjectFromWpPost::DECORATE_FILTER_NAME,
            fn(PostObjectInterface $postObject): PostObjectInterface => $this->maybeDecorateMirroredPost($postObject)
        );
    }

    /**
     * Maybe decorate the post object with mirrored post functionality.
     *
     * @param PostObjectInterface $postObject The post object to decorate.
     * @return PostObjectInterface The decorated post object.
     */
    private function maybeDecorateMirroredPost(PostObjectInterface $postObject): PostObjectInterface
    {
        $otherBlogId = $this->getOtherBlogId->getOtherBlogId();
        if ($otherBlogId === null) {
            return $postObject;
        }

        return new MirroredPostObject($postObject, $this->wpService, $otherBlogId);
    }
}
