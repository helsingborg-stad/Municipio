<?php

namespace Municipio\PostFilters;

use Municipio\HooksRegistrar\Hookable;
use WP_Post;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetBlogIdFromUrl;
use WpService\Contracts\GetCurrentBlogId;
use WpService\Contracts\GetPermalink;
use WpService\Contracts\IsMultisite;
use WpService\Contracts\RestoreCurrentBlog;
use WpService\Contracts\SwitchToBlog;

/**
 * Allow posts from other sites to keep their permalinks.
 */
class AllowPostsFromOtherSitesToKeepTheirPermalinks implements Hookable
{
    /**
     * Constructor.
     */
    public function __construct(
        private AddFilter&IsMultisite&GetCurrentBlogId&GetBlogIdFromUrl&SwitchToBlog&GetPermalink&RestoreCurrentBlog $wpService
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('post_link', [$this, 'getPermalinkFromOtherSite'], 10, 3);
    }

    /**
     * Get permalink from other site.
     *
     * @param string $permalink
     * @param WP_Post $post
     * @param bool $leavename
     * @return string
     */
    public function getPermalinkFromOtherSite(string $permalink, WP_Post $post, bool $leavename): string
    {
        if (!$this->wpService->isMultisite()) {
            return $permalink;
        }

        $currentSiteId = $this->wpService->GetCurrentBlogId();
        $parsedUrl     = parse_url($post->guid);
        $domain        = $parsedUrl['host'];
        $domain       .= isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
        $path          = $parsedUrl['path'];
        $siteId        = $this->wpService->getBlogIdFromUrl($domain, $path);

        if ($siteId === $currentSiteId) {
            return $permalink;
        }

        $this->wpService->switchToBlog($siteId);
        $permalink = $this->wpService->getPermalink($post->ID);
        $this->wpService->restoreCurrentBlog();

        return $permalink;
    }
}
