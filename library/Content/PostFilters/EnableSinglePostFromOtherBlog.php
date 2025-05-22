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
 * EnableSinglePostFromOtherBlog.
 *
 * This class enables the ability to fetch a single post from another blog in a multisite WordPress setup.
 * It modifies the main query to switch to the specified blog and fetch the post data.
 */
class EnableSinglePostFromOtherBlog implements Hookable // phpcs:ignore PSR1.Classes.ClassDeclaration.MultipleClasses
{
    /**
     * Constructor
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
        $this->wpService->addAction('pre_get_posts', [$this, 'enableSinglePostFromOtherBlog']);
    }

    /**
     * Enable single post from another blog.
     *
     * This method modifies the main query to fetch a single post from another blog in a multisite setup.
     * It switches to the specified blog and sets the post type for the query.
     *
     * @param WP_Query $query The WP_Query object.
     */
    public function enableSinglePostFromOtherBlog(WP_Query $query): void
    {
        $otherBlogId = $this->getOtherBlogId();

        // Only run for the main query, not in admin, and only if blog_id and p (post ID) are set in the URL.
        if (!$query->is_main_query() || $this->wpService->isAdmin() || is_null($otherBlogId)) {
            return;
        }

        $postId = !empty($_GET['p']) ? (int) $_GET['p'] : null;

        if (!$postId) {
            return;
        }

        // Switch to the specified blog.
        $this->wpService->switchToBlog($otherBlogId);

        // Set the query's post_type to match the fetched post's type.
        // This is necessary to ensure that the post is fetched correctly, and will fail if not set.
        $query->set('post_type', $this->wpService->getPostType($postId));

        // Restore the original blog after the posts have been retrieved.
        $this->wpService->addFilter('the_posts', [$this, 'restoreToCurrentBlogAfterQuery'], 10, 1);
    }

    /**
     * Restore to the current blog after the query.
     */
    public function restoreToCurrentBlogAfterQuery(array $posts): array
    {
        $this->wpService->removeFilter('the_posts', [$this, 'restoreToCurrentBlogAfterQuery']);
        $this->wpService->restoreCurrentBlog();
        return $posts;
    }

    /**
     * Get the ID of the other blog.
     *
     * This method retrieves the ID of the other blog based on the current request parameters.
     * It checks if the request is for a multisite setup and if the blog_id and p parameters are set.
     *
     * @return int|null The ID of the other blog or null if not applicable.
     */
    private function getOtherBlogId(): ?int
    {
        if ($this->wpService->isMultiSite() && $this->wpService->msIsSwitched()) {
            return $this->wpService->getCurrentBlogId();
        }

        if ($this->wpService->isMultiSite() && !empty($_GET['blog_id']) && !empty($_GET['p'])) {
            return (int) $_GET['blog_id'];
        }

        return null;
    }
}
