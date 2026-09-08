<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V62;

use Municipio\Upgrade\VersionInterface;
use Override;
use WpService\Contracts\GetPosts;
use WpService\Contracts\GetPostTypes;
use WpService\Contracts\UpdatePostMeta;

/**
 * Runs the v62 migration for search exclusion meta keys.
 * Migrates the "exclude_from_search" post meta key to the SEO framework's "exclude_local_search" post meta key.
 */
class Version62 implements VersionInterface
{
    public function __construct(private GetPosts&GetPostTypes&UpdatePostMeta $wpService
    )
    {
    }

    public function upgradeToVersion(): void
    {
        $legacyThemeKey = 'exclude_from_search';
        $seoFrameworkMetaKey = 'exclude_local_search';
        $posts = $this->wpService->getPosts(array(
            'post_type' => $this->wpService->getPostTypes(['public' => true]),
            'meta_key' => $legacyThemeKey,
            'meta_value' => '1',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ));

        foreach ($posts as $postId) {
            $this->wpService->updatePostMeta($postId, $seoFrameworkMetaKey, '1');
        }
    }
}
