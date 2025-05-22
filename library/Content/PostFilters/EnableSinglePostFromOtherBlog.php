<?php

namespace Municipio\Content\PostFilters;

use Municipio\Content\PostFilters\Contracts\BlogIdQueryVar;
use Municipio\HooksRegistrar\Hookable;
use WP_Query;
use WpService\Contracts\{
    AddAction,
    AddFilter,
    GetCurrentBlogId,
    GetPostType,
    GetQueryVar,
    IsAdmin,
    IsMultisite,
    MsIsSwitched,
    RemoveFilter,
    RestoreCurrentBlog,
    SwitchToBlog
};

/**
 * Enables fetching a single post from another blog in a multisite WordPress setup.
 */
class EnableSinglePostFromOtherBlog implements Hookable
{
    /**
     * Constructor for the EnableSinglePostFromOtherBlog class.
     *
     * @param AddAction&AddFilter&IsAdmin&SwitchToBlog&GetPostType&RemoveFilter&RestoreCurrentBlog&IsMultisite&MsIsSwitched&GetCurrentBlogId&GetQueryVar $wpService
     *        A service object that provides various WordPress-related actions and filters, including blog switching,
     *        post type retrieval, multisite checks, and admin checks.
     */
    public function __construct(
        private AddAction&AddFilter&IsAdmin&SwitchToBlog&GetPostType&RemoveFilter&RestoreCurrentBlog&IsMultisite&MsIsSwitched&GetCurrentBlogId&GetQueryVar $wpService,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('pre_get_posts', [$this, 'handlePreGetPosts']);
    }

    /**
     * Modifies the main query to fetch a single post from another blog in a multisite setup.
     */
    public function handlePreGetPosts(WP_Query $query): void
    {
        if (!$this->shouldHandleQuery($query)) {
            return;
        }

        $otherBlogId = $this->getRequestedBlogId();
        $postId      = $this->getRequestedPostId();

        $this->wpService->addFilter('the_posts', [$this, 'restoreOriginalBlog'], 10, 1);
        $this->wpService->switchToBlog($otherBlogId);
        $query->set('post_type', $this->wpService->getPostType($postId));
    }

    /**
     * Restores the original blog after the posts have been retrieved.
     */
    public function restoreOriginalBlog(array $posts): array
    {
        $this->wpService->removeFilter('the_posts', [$this, __FUNCTION__]);
        $this->wpService->restoreCurrentBlog();
        return $posts;
    }

    /**
     * Determines if the current query should be handled.
     */
    private function shouldHandleQuery(WP_Query $query): bool
    {
        return $query->is_main_query()
            && !$this->wpService->isAdmin()
            && $this->getRequestedBlogId() !== null
            && $this->getRequestedPostId() !== null;
    }

    /**
     * Retrieves the requested blog ID from the query parameters.
     */
    private function getRequestedBlogId(): ?int
    {
        if (!$this->wpService->isMultiSite()) {
            return null;
        };

        if ($this->wpService->msIsSwitched()) {
            return $this->wpService->getCurrentBlogId();
        }

        $blogId = $this->wpService->getQueryVar(BlogIdQueryVar::BLOG_ID_QUERY_VAR, null);

        if ($blogId !== null && is_numeric($blogId)) {
            return (int) $blogId;
        }

        return null;
    }

    /**
     * Retrieves the requested post ID from the query parameters.
     */
    private function getRequestedPostId(): ?int
    {
        $postId = $this->wpService->getQueryVar('p', null);

        if ($postId !== null && is_numeric($postId)) {
            return (int) $postId;
        }

        return null;
    }
}
