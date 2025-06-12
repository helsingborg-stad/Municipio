<?php

namespace Municipio\MirroredPost\Utils\GetOtherBlogId;

use Municipio\MirroredPost\Contracts\BlogIdQueryVar;
use WpService\Contracts\{
    GetCurrentBlogId,
    GetQueryVar,
    IsMultisite,
    MsIsSwitched
};

/**
 * Retrieves the ID of another blog in a multisite WordPress installation.
 */
class GetOtherBlogId implements GetOtherBlogIdInterface
{
    /**
     * Constructor.
     *
     * @param IsMultisite&MsIsSwitched&GetQueryVar&GetCurrentBlogId $wpService
     */
    public function __construct(
        private IsMultisite&MsIsSwitched&GetQueryVar&GetCurrentBlogId $wpService
    ) {
    }

    /**
     * Retrieves the ID of another blog if the current post is from a different blog.
     *
     * @return int|null The ID of the other blog, or null if not applicable.
     */
    public function getOtherBlogId(): ?int
    {
        if ($this->isInSwitchedState()) {
            return $this->wpService->getCurrentBlogId();
        }

        if ($this->isSinglePostFromOtherBlog()) {
            return (int) $this->getBlogIdFromQuery();
        }

        return null;
    }

    /**
     * Checks if the current WordPress installation is in a switched state.
     *
     * @return bool True if in switched state, false otherwise.
     */
    private function isInSwitchedState(): bool
    {
        return $this->wpService->isMultiSite()
            && $this->wpService->msIsSwitched();
    }

    /**
     * Checks if the current request is for a single post from another blog.
     *
     * @return bool True if the request is for a single post from another blog, false otherwise.
     */
    private function isSinglePostFromOtherBlog(): bool
    {
        return $this->hasPostId() && $this->hasBlogId();
    }

    /**
     * Checks if the current request has a post ID set.
     *
     * @return bool True if a post ID is set, false otherwise.
     */
    private function hasPostId(): bool
    {
        return !is_null($this->wpService->getQueryVar('p', null));
    }

    /**
     * Checks if the current request has a blog ID set.
     *
     * @return bool True if a blog ID is set, false otherwise.
     */
    private function hasBlogId(): bool
    {
        return !is_null($this->getBlogIdFromQuery());
    }

    /**
     * Retrieves the blog ID from the query variable.
     *
     * @return mixed The blog ID if set, null otherwise.
     */
    private function getBlogIdFromQuery(): mixed
    {
        return $this->wpService->getQueryVar(BlogIdQueryVar::BLOG_ID_QUERY_VAR, null);
    }
}
