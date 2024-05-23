<?php

namespace Municipio\ExternalContent\PostsResults\Helpers;

use Municipio\ExternalContent\Sources\ISourceRegistry;
use WP_Query;

class Helpers implements IsQueryForExternalContent, GetSourcesByPostType
{
    public function __construct(private ISourceRegistry $sourceRegistry)
    {
    }

    public function isQueryForExternalContent(WP_Query $query): bool
    {
        if (empty($query->get('post_type'))) {
            return false;
        }

        foreach ($this->sourceRegistry->getSources() as $source) {
            if ($source->getPostType() === $query->get('post_type')) {
                return true;
            }
        }

        return false;
    }

    public function getSourcesByPostType(string $postType): array
    {
        return array_filter($this->sourceRegistry->getSources(), function ($source) use ($postType) {
            return $source->getPostType() === $postType;
        });
    }
}
