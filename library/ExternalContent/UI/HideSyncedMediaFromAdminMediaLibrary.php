<?php

namespace Municipio\ExternalContent\UI;

use Municipio\HooksRegistrar\Hookable;
use WP_Query;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetCurrentScreen;
use WpService\Contracts\IsAdmin;

/**
 * Class HideSyncedMediaFromAdminMediaLibrary
 *
 * This class hides synced media from the admin media library.
 */
class HideSyncedMediaFromAdminMediaLibrary implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(private string $metaKey, private AddAction&IsAdmin $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('pre_get_posts', [$this, 'preGetPosts']);
    }

    /**
     * Pre get posts.
     *
     * This method is called before the query is executed.
     * Which makes it where we modify the query to exclude synced media.
     *
     * @param WP_Query $query
     */
    public function preGetPosts(WP_Query &$query): void
    {
        $postType = $query->query['post_type'] ?? '';

        if (!$this->wpService->isAdmin() || $postType !== 'attachment') {
            return;
        }

        $metaQuery   = $query->query_vars['meta_query'] ?? [];
        $metaQuery[] = [
            'key'     => $this->metaKey,
            'compare' => 'NOT EXISTS',
        ];

        $query->query_vars['meta_query'] = $metaQuery;
    }
}
