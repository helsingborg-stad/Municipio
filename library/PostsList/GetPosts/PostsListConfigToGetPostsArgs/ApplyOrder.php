<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/*
 * Apply order from posts list config to get posts args
 */
class ApplyOrder implements ApplyPostsListConfigToGetPostsArgsInterface
{
    /**
     * Apply order from posts list config to get posts args
     *
     * @param GetPostsConfigInterface $config
     * @param array $args
     * @return array
     */
    public function apply(GetPostsConfigInterface $config, array $args): array
    {
        return [
            ...$args,
            ...$this->getOrderByArgs($config),
            'order' => $config->getOrder()->value,
        ];
    }

    private function getOrderByArgs(GetPostsConfigInterface $config): array
    {
        $orderBy              = $config->getOrderBy();
        $validPostTableFields = ['title', 'date', 'modified'];
        $normalizedOrderBy    = $this->normalizePostTableFieldName($orderBy);

        if (in_array($normalizedOrderBy, $validPostTableFields, true)) {
            return ['orderby' => $normalizedOrderBy];
        }

        return $this->getOrderByArgsForCustomFields($normalizedOrderBy);
    }

    private function normalizePostTableFieldName(string $fieldName): string
    {
        return match ($fieldName) {
            'post_title' => 'title',
            'post_date'  => 'date',
            'post_modified' => 'modified',
            default => $fieldName,
        };
    }

    private function getOrderByArgsForCustomFields(string $orderBy): array
    {
        return [
            'orderby'  => 'meta_value',
            'meta_key' => $orderBy,
        ];
    }
}
