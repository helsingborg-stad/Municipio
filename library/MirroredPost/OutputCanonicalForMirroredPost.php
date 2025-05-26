<?php

namespace Municipio\MirroredPost;

use Municipio\MirroredPost\Utils\GetOtherBlogId\GetOtherBlogIdInterface;
use Municipio\MirroredPost\Utils\IsMirroredPost\IsMirroredPostInterface;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddFilter;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\IsSingle;
use WpService\Contracts\GetQueryVar;
use WpService\Contracts\GetSiteUrl;
use WpService\Contracts\GetPermalink;
use WpService\Contracts\RestoreCurrentBlog;
use WpService\Contracts\SwitchToBlog;

/**
 * Outputs a canonical link tag in wp_head for mirrored posts.
 */
class OutputCanonicalForMirroredPost
{
    /**
     * Constructor.
     */
    public function __construct(
        private AddAction&IsSingle&GetQueryVar&GetSiteUrl&GetPermalink&AddFilter&ApplyFilters&SwitchToBlog&RestoreCurrentBlog $wpService,
        private GetOtherBlogIdInterface&IsMirroredPostInterface $utils,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function addHooks(): void
    {
        $this->wpService->addFilter('get_canonical_url', [$this, 'outputCanonicalTag']);
        $this->wpService->addFilter('the_seo_framework_meta_render_data', [$this, 'accountForSeoFrameworkPlugin']);
    }

    /**
     * Outputs the canonical tag for mirrored posts.
     *
     * @param string $canonicalUrl The canonical URL to output.
     * @return string The modified canonical URL if the post is mirrored, otherwise the original URL.
     */
    public function outputCanonicalTag($canonicalUrl): string
    {
        if (!$this->shouldOutputCanonicalTag()) {
            return $canonicalUrl;
        }

        $otherBlogId = $this->utils->getOtherBlogId();

        $this->wpService->switchToBlog($otherBlogId);
        $permalink = $this->wpService->getPermalink();
        $this->wpService->restoreCurrentBlog();

        return $permalink;
    }

    /**
     * Adjusts the canonical URL for the SEO Framework plugin.
     *
     * @param array $data The data array containing the canonical URL.
     * @return array The modified data array with the adjusted canonical URL.
     */
    public function accountForSeoFrameworkPlugin(array $data): array
    {
        if ($this->utils->isMirrored() && isset($data['canonical']) && !empty($data['canonical']['attributes']['href'])) {
            $data['canonical']['attributes']['href'] = $this->wpService->applyFilters('get_canonical_url', $data['canonical']['attributes']['href']);
        }

        return $data;
    }

    /**
     * Determines if the canonical tag should be output.
     *
     * @return bool True if the post is mirrored, false otherwise.
     */
    private function shouldOutputCanonicalTag(): bool
    {
        return $this->utils->isMirrored();
    }
}
