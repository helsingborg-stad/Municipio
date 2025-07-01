<?php

namespace Municipio\Toc;

use Municipio\PostObject\Factory\CreatePostObjectFromWpPost;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Toc\PostObject\TocPostObject;
use Municipio\Toc\Utils\TocUtils;
use Municipio\Toc\Utils\TocUtilsInterface;
use WpService\WpService;

/**
 * Enables the Table of Contents feature in WordPress.
 */
class TocFeature
{
    private TocUtilsInterface $tocUtils;

    /**
     * Constructor.
     *
     * @param WpService $wpService The WordPress service instance.
     */
    public function __construct(private WpService $wpService)
    {
        $this->tocUtils = $this->createTocUtils();
    }

    /**
     * Enable the Table of Contents feature.
     *
     * This method sets up the necessary hooks and filters to integrate
     * the TOC functionality with the PostObject system.
     */
    public function enable(): void
    {
        $this->decoratePostObject();
    }

    /**
     * Decorate the post object.
     *
     * This method sets up a filter to decorate the post object with TOC functionality.
     */
    private function decoratePostObject(): void
    {
        $this->wpService->addFilter(
            CreatePostObjectFromWpPost::DECORATE_FILTER_NAME,
            fn(PostObjectInterface $postObject): PostObjectInterface => $this->maybeDecorateTocPost($postObject)
        );
    }

    /**
     * Maybe decorate the post object with TOC functionality.
     *
     * @param PostObjectInterface $postObject The post object to decorate.
     * @return PostObjectInterface The decorated post object.
     */
    private function maybeDecorateTocPost(PostObjectInterface $postObject): PostObjectInterface
    {
        if (!$this->tocUtils->shouldEnableToc($postObject)) {
            return $postObject;
        }

        return new TocPostObject($postObject, $this->wpService, $this->tocUtils);
    }

    /**
     * Create an instance of TocUtils.
     *
     * @return TocUtils The TocUtils instance.
     */
    private function createTocUtils(): TocUtils
    {
        return new TocUtils($this->wpService);
    }
}