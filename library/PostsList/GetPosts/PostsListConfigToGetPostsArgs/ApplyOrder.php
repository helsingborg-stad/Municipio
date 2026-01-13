<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/*
 * Apply order from posts list config to get posts args
 */
class ApplyOrder implements ApplyPostsListConfigToGetPostsArgsInterface
{
    public function __construct(
        private AppearanceConfigInterface $appearanceConfig,
    ) {}

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
        ];
    }

    /**
     * Get order by args from config
     *
     * @param GetPostsConfigInterface $config
     * @return array
     */
    private function getOrderByArgs(GetPostsConfigInterface $config): array
    {
        $orderBy = $config->getOrderBy();
        $validPostTableFields = ['title', 'date', 'modified'];
        $normalizedOrderBy = $this->normalizePostTableFieldName($orderBy);

        if (in_array($normalizedOrderBy, $validPostTableFields, true)) {
            return ['orderby' => $normalizedOrderBy, 'order' => $config->getOrder()->value];
        }

        return $this->getOrderByArgsForCustomFields($normalizedOrderBy, $config);
    }

    /**
     * Normalize post table field name
     *
     * @param string $fieldName
     * @return string
     */
    private function normalizePostTableFieldName(string $fieldName): string
    {
        return match ($fieldName) {
            'post_title' => 'title',
            'post_date' => 'date',
            'post_modified' => 'modified',
            default => $fieldName,
        };
    }

    /**
     * Get order by args for custom fields
     *
     * @param string $orderBy
     * @return array
     */
    private function getOrderByArgsForCustomFields(string $orderBy, GetPostsConfigInterface $config): array
    {
        $invalidPostMetaFieldKeys = [null, '', 'none'];
        if (in_array($orderBy, $invalidPostMetaFieldKeys, true)) {
            return [];
        }

        if ($this->shouldUseDateClause($orderBy, $config)) {
            return [
                'orderby' => [
                    ApplyDate::META_QUERY_KEY => $config->getOrder()->value,
                ],
            ];
        }

        return [
            'orderby' => 'meta_value',
            'meta_key' => $orderBy,
            'order' => $config->getOrder()->value,
        ];
    }

    private function shouldUseDateClause(string $customFieldKey, GetPostsConfigInterface $config): bool
    {
        if ($customFieldKey !== $this->appearanceConfig->getDateSource()) {
            return false;
        }

        if (is_null($config->getDateFrom()) && is_null($config->getDateTo())) {
            return false;
        }

        return true;
    }
}
