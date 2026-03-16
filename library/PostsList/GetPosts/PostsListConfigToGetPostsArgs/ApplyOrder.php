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
        // Ensure date clause exists if we'll need it for ordering
        if ($this->willNeedDateClauseForOrdering($config)) {
            $args = DateClauseBuilder::ensureDateClauseForOrdering($args, $config);
        }

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
                    MetaQueryKeys::DATE_CLAUSE => $config->getOrder()->value,
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

        return true;
    }

    /**
     * Check if we will need a date clause for ordering
     *
     * @param GetPostsConfigInterface $config
     * @return bool
     */
    private function willNeedDateClauseForOrdering(GetPostsConfigInterface $config): bool
    {
        $orderBy = $config->getOrderBy();
        $validPostTableFields = ['title', 'date', 'modified'];
        $normalizedOrderBy = $this->normalizePostTableFieldName($orderBy);

        // We don't need date clause for post table fields
        if (in_array($normalizedOrderBy, $validPostTableFields, true)) {
            return false;
        }

        // Check if this is a custom field that would use date clause
        return $this->shouldUseDateClause($normalizedOrderBy, $config);
    }
}
