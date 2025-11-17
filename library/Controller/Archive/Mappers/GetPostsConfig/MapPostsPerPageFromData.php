<?php

namespace Municipio\Controller\Archive\Mappers\GetPostsConfig;

use WpService\Contracts\GetThemeMod;
use Municipio\Controller\Archive\Mappers\MapperInterface;

class MapPostsPerPageFromData implements MapperInterface
{
    public function __construct(private GetThemeMod $wpService)
    {
    }

    public function map(array $data): int
    {
        return (int)$this->wpService->getThemeMod('archive_' . $this->getPostTypeFromData($data) . '_post_count', $this->wpService->getThemeMod('archive_post_post_count', 10));
    }

    private function getPostTypeFromData(array $data): string
    {
        return !empty($data['postType']) ? $data['postType'] : 'page';
    }
}
