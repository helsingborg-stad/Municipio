<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use WpService\Contracts\GetCurrentBlogId;
use WpService\Contracts\HomeUrl;
use WpService\Contracts\IsMultisite;

/**
 * Maps a stable post ID across WordPress sites.
 */
class MapUUID implements PropertyMapperInterface
{
    public function __construct(private HomeUrl&IsMultisite&GetCurrentBlogId $wpService)
    {
    }

    /**
     * Map the provider object ID.
     */
    public function mapProperty(\WP_Post $post): string
    {
        return sprintf(
            '%s-%s-%d',
            str_replace('.', '-', $this->getHost()),
            $this->getSite(),
            $post->ID,
        );
    }

    /**
     * Get the host component of the site URL.
     */
    private function getHost(): string
    {
        return (string) parse_url($this->wpService->homeUrl(), PHP_URL_HOST);
    }

    /**
     * Get the multisite blog ID component.
     */
    private function getSite(): string
    {
        return $this->wpService->isMultisite() ? (string) $this->wpService->getCurrentBlogId() : '0';
    }
}