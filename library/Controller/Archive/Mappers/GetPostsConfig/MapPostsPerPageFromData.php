<?php

namespace Municipio\Controller\Archive\Mappers\GetPostsConfig;

use WpService\Contracts\GetThemeMod;
use Municipio\Controller\Archive\Mappers\MapperInterface;

/**
 * Map posts per page from data
 */
class MapPostsPerPageFromData implements MapperInterface
{
    /**
     * Constructor
     */
    public function __construct(private GetThemeMod $wpService)
    {
    }

    /**
     * Map posts per page from data
     *
     * @param array $data
     * @return int
     */
    public function map(array $data): int
    {
        return (int)$this->wpService->getThemeMod('archive_' . $this->getPostTypeFromData($data) . '_post_count', $this->wpService->getThemeMod('archive_post_post_count', 10));
    }

    /**
     * Get post type from data
     *
     * @param array $data
     * @return string
     */
    private function getPostTypeFromData(array $data): string
    {
        return !empty($data['postType']) ? $data['postType'] : 'page';
    }
}
