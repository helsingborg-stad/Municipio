<?php

namespace Municipio\MirroredPost;

use Municipio\HooksRegistrar\Hookable;
use Municipio\MirroredPost\Utils\GetOtherBlogId\GetOtherBlogIdInterface;
use WP_Query;
use WpService\WpService;

/**
 * Enables fetching a single post from another blog in a multisite WordPress setup.
 */
class EnableSingleMirroredPostInWpQuery implements Hookable
{
    /**
     * Constructor for the EnableSinglePostFromOtherBlog class.
     *
     * @param WpService $wpService
     */
    public function __construct(
        private WpService $wpService,
        private GetOtherBlogIdInterface $mirroredPostUtils
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
        if (!$this->shouldAlterQuery($query)) {
            return;
        }

        $otherBlogId = $this->mirroredPostUtils->getOtherBlogId();
        $postId      = $this->getRequestedPostId();

        $this->wpService->switchToBlog($otherBlogId);
        $query->set('post_type', $this->wpService->getPostType($postId));
        $this->wpService->addFilter('the_posts', [$this, 'restoreOriginalBlog'], 10, 1);
    }

    /**
     * Restores the original blog after the posts have been retrieved.
     *
     * @param \WP_Post[] $posts The posts retrieved from the other blog.
     */
    public function restoreOriginalBlog(array $posts): array
    {
        $this->wpService->removeFilter('the_posts', [$this, __FUNCTION__]);
        $this->wpService->restoreCurrentBlog();

        if (!empty($posts)) {
            $this->maybeSetupPostTypeOnOriginalBlog($posts[0]->post_type);
        }

        return $posts;
    }

    /**
     * Sets up the post type on the original blog if it is not registered.
     *
     * @param string $postType The post type to check and register if necessary.
     */
    private function maybeSetupPostTypeOnOriginalBlog(string $postType): void
    {
        // if post type is not registeered, register it to be able to use it.
        if (!$this->wpService->postTypeExists($postType)) {
            $this->wpService->registerPostType($postType, [ 'public' => true ]);
        }
    }

    /**
     * Determines if the current query should be altered.
     */
    private function shouldAlterQuery(WP_Query $query): bool
    {
        return $query->is_main_query()
            && !$this->wpService->isAdmin()
            && $this->mirroredPostUtils->getOtherBlogId() !== null
            && $this->getRequestedPostId() !== null;
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
