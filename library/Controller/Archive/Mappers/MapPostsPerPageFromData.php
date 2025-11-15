<?php

namespace Municipio\Controller\Archive\Mappers;

use WpService\Contracts\GetThemeMod;

class MapPostsPerPageFromData implements MapperInterface
{
    public function __construct(private string $postType, private GetThemeMod $wpService)
    {
    }

    public function map(array $data): int
    {
        return (int)$this->wpService->getThemeMod('archive_' . $this->postType . '_post_count', $this->wpService->getThemeMod('archive_post_post_count', 10));
    }
}
