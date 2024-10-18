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

class AllowPostsFromOtherSitesToKeepTheirPermalinks implements Hookable
{
    public function __construct(
        private AddFilter&IsMultisite&GetCurrentBlogId&GetBlogIdFromUrl&SwitchToBlog&GetPermalink&RestoreCurrentBlog $wpService
    ) {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('post_link', [$this, 'getPermalinkFromOtherSite'], 10, 3);
    }

    public function getPermalinkFromOtherSite($permalink, $post, $leavename): string
    {
        if (!is_a($post, WP_Post::class) || !$this->wpService->isMultisite()) {
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
