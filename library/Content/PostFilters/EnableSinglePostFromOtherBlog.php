<?php

namespace Municipio\Content\PostFilters;

use Municipio\HooksRegistrar\Hookable;
use WP_Query;
use WpService\Contracts\{
    AddAction,
    AddFilter,
    GetCurrentBlogId,
    GetPostType,
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
     * @param AddAction&AddFilter&IsAdmin&SwitchToBlog&GetPostType&RemoveFilter&RestoreCurrentBlog&IsMultisite&MsIsSwitched&GetCurrentBlogId $wpService
     *        A service object that provides various WordPress-related actions and filters, including blog switching,
     *        post type retrieval, multisite checks, and admin checks.
     */
    public function __construct(
        private AddAction&AddFilter&IsAdmin&SwitchToBlog&GetPostType&RemoveFilter&RestoreCurrentBlog&IsMultisite&MsIsSwitched&GetCurrentBlogId $wpService,
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

        $this->wpService->switchToBlog($otherBlogId);
        $query->set('post_type', $this->wpService->getPostType($postId));
        $this->wpService->addFilter('the_posts', [$this, 'restoreOriginalBlog'], 10, 1);
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

        if (!empty($_GET['blog_id']) && !empty($_GET['p']) && is_numeric($_GET['blog_id'])) {
            return (int) $_GET['blog_id'];
        }

        return null;
    }

    /**
     * Retrieves the requested post ID from the query parameters.
     */
    private function getRequestedPostId(): ?int
    {
        return !empty($_GET['p']) && is_numeric($_GET['p'])
            ? (int) $_GET['p']
            : null;
    }
}
