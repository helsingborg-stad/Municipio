<?php

namespace Municipio\MirroredPost;

use Municipio\MirroredPost\Contracts\BlogIdQueryVar;
use Municipio\MirroredPost\PostObject\MirroredPostObject;
use Municipio\MirroredPost\Utils\GetOtherBlogId\GetOtherBlogId;
use Municipio\MirroredPost\Utils\IsMirroredPost\IsMirroredPost;
use Municipio\MirroredPost\Utils\MirroredPostUtils;
use Municipio\MirroredPost\Utils\MirroredPostUtilsInterface;
use Municipio\PostObject\Factory\CreatePostObjectFromWpPost;
use Municipio\PostObject\PostObjectInterface;
use WpService\WpService;

/**
 * Enables the Mirrored Post feature in WordPress.
 */
class MirroredPostFeature
{
    private MirroredPostUtilsInterface $mirroredPostUtils;

    /**
     * Constructor.
     *
     * @param WpService $wpService The WordPress service instance.
     */
    public function __construct(private WpService $wpService)
    {
        $this->mirroredPostUtils = $this->createMirroredPostUtils();
    }

    /**
     * Enable the Mirrored Post feature.
     *
     * This method sets up the necessary hooks and filters to enable the Mirrored Post functionality.
     */
    public function enable(): void
    {
        $this->addBlogIdQueryVarHook();
        $this->enableSingleMirroredPostInWpQuery();
        $this->decoratePostObject();
        $this->outputCanonicalForMirroredPost();
    }

    /**
     * Add the blog ID query variable hook.
     *
     * This method adds a query variable for the blog ID to the WordPress query.
     */
    private function addBlogIdQueryVarHook(): void
    {
        (new BlogIdQueryVar($this->wpService))->addHooks();
    }

    /**
     * Enable single mirrored post in WP_Query.
     *
     * This method sets up the necessary hooks to enable single mirrored post functionality in WP_Query.
     */
    private function enableSingleMirroredPostInWpQuery(): void
    {
        (new EnableSingleMirroredPostInWpQuery($this->wpService, $this->mirroredPostUtils))->addHooks();
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
        $otherBlogId = $this->mirroredPostUtils->getOtherBlogId();
        if ($otherBlogId === null) {
            return $postObject;
        }
        return new MirroredPostObject($postObject, $this->wpService, $otherBlogId);
    }

    /**
     * Output canonical URL for mirrored posts.
     *
     * This method sets up the necessary hooks to output the canonical URL for mirrored posts.
     */
    private function outputCanonicalForMirroredPost(): void
    {
        (new OutputCanonicalForMirroredPost(
            $this->wpService,
            $this->mirroredPostUtils
        ))->addHooks();
    }

    /**
     * Create an instance of MirroredPostUtils.
     *
     * @return MirroredPostUtils The MirroredPostUtils instance.
     */
    private function createMirroredPostUtils(): MirroredPostUtils
    {
        return new MirroredPostUtils(
            new IsMirroredPost($this->wpService),
            new GetOtherBlogId($this->wpService)
        );
    }
}
