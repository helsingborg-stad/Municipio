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
        $this->wpService->addFilter('post_link', [$this, 'getPermalinkFromOtherSite'], 10, 2);
    }

    /**
     * Get permalink from other site.
     *
     * @param string $permalink
     * @param WP_Post $post
     * @param bool $leavename
     * @return string
     */
    public function getPermalinkFromOtherSite(string $permalink, WP_Post $post): string
    {
        if (!$this->wpService->isMultisite()) {
            return $permalink;
        }

        $currentSiteId = $this->wpService->getCurrentBlogId();
        $otherSitesId  = $this->getSiteIdFromPostGuid($post->guid);

        if ($otherSitesId === $currentSiteId) {
            return $permalink;
        }

        return $this->getOtherSitesPostLink($otherSitesId, $post);
    }

    /**
     * Get other sites post link.
     *
     * @param int $otherSitesId
     * @param WP_Post $post
     * @return string
     */
    private function getOtherSitesPostLink(int $otherSitesId, WP_Post $post): string
    {
        $this->wpService->switchToBlog($otherSitesId);
        $permalink = $this->wpService->getPermalink($post->ID);
        $this->wpService->restoreCurrentBlog();

        return $permalink;
    }

    /**
     * Get site ID from post GUID.
     *
     * @param string $guid
     * @return int
     */
    private function getSiteIdFromPostGuid(string $guid): int
    {
        $parsedUrl = parse_url($guid);
        $domain    = $parsedUrl['host'] . (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '');

        return $this->wpService->getBlogIdFromUrl($domain, $parsedUrl['path']);
    }
}
