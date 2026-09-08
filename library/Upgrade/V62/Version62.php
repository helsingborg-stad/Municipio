<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V62;

use Municipio\Upgrade\VersionInterface;
use Override;
use WpService\Contracts\GetPosts;
use WpService\Contracts\UpdatePostMeta;

/**
 * Runs the v62 migration for search exclusion meta keys.
 * Migrates the "exclude_from_search" meta key to the SEO framework's "exclude_local_search" meta key.
 */
class Version62 implements VersionInterface
{
    public function __construct(private GetPosts&UpdatePostMeta $wpService
    )
    {
    }

    public function upgradeToVersion(): void
    {
        $legacyThemeKey = 'exclude_from_search';
        $seoFrameworkMetaKey = 'exclude_local_search';
        $posts = $this->wpService->getPosts(array(
            'post_type' => 'any',
            'meta_key' => $legacyThemeKey,
            'meta_value' => '1',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ));

        foreach ($posts as $postId) {
            $this->wpService->updatePostMeta($postId, $seoFrameworkMetaKey, '1');
        }
    }
}
