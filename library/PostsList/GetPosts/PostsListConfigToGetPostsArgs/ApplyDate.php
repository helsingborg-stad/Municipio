<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/*
 * Apply date from posts list config to get posts args
 */
class ApplyDate implements ApplyPostsListConfigToGetPostsArgsInterface
{
    public const META_QUERY_KEY = 'date_clause';

    /**
     * Apply date from posts list config to get posts args
     *
     * @param GetPostsConfigInterface $config
     * @param array $args
     * @return array
     */
    public function apply(GetPostsConfigInterface $config, array $args): array
    {
        return [
            ...$args,
            ...$this->getDateOrMetaQuery($config),
        ];
    }

    /**
     * Get date_query or meta_query depending on config
     *
     * @param GetPostsConfigInterface $config
     * @return array
     */
    private function getDateOrMetaQuery(GetPostsConfigInterface $config): array
    {
        if ($this->shouldApplyDateQuery($config)) {
            return ['date_query' => $this->buildDateQuery($config)];
        }

        if ($this->shouldApplyMetaQuery($config)) {
            return ['meta_query' => $this->buildMetaQuery($config)];
        }

        return [];
    }

    /**
     * Determine if date_query should be applied
     *
     * @param GetPostsConfigInterface $config
     * @return bool
     */
    private function shouldApplyDateQuery(GetPostsConfigInterface $config): bool
    {
        $column = $config->getDateSource();

        return in_array($column, ['post_date', 'post_modified']);
    }

    /**
     * Build the date_query array
     *
     * @param GetPostsConfigInterface $config
     * @return array
     */
    private function buildDateQuery(GetPostsConfigInterface $config): array
    {
        $query = ['column' => $config->getDateSource()];
        if ($config->getDateFrom()) {
            $query['after'] = $config->getDateFrom();
        }
        if ($config->getDateTo()) {
            $query['before'] = $config->getDateTo();
        }
        return [...$query, 'inclusive' => true];
    }

    /**
     * Determine if meta_query should be applied
     *
     * @param GetPostsConfigInterface $config
     * @return bool
     */
    private function shouldApplyMetaQuery(GetPostsConfigInterface $config): bool
    {
        $column = $config->getDateSource();
        $invalidDateSourceValues = ['post_date', 'post_modified', 'none', null, ''];
        return !in_array($column, $invalidDateSourceValues, true);
    }

    /**
     * Build the meta_query array
     *
     * @param GetPostsConfigInterface $config
     * @return array
     */
    private function buildMetaQuery(GetPostsConfigInterface $config): array
    {
        $dateFrom = $config->getDateFrom() ?: false;
        $dateTo = $config->getDateTo() ?: false;

        $dateFrom = $dateFrom ? date('Y-m-d 00:00:00', strtotime($dateFrom)) : null;
        $dateTo = $dateTo ? date('Y-m-d 23:59:59', strtotime($dateTo)) : null;

        if (is_null($dateFrom) && is_null($dateTo)) {
            $dateFrom = '0001-01-01 00:00:00';
            $dateTo = '9999-12-31 23:59:59';
            $compare = 'BETWEEN';
        } else {
            $compare = $dateFrom && $dateTo ? 'BETWEEN' : ($dateFrom ? '>=' : '<=');
        }

        $value = $dateFrom && $dateTo ? [$dateFrom, $dateTo] : ($dateFrom ?: $dateTo);

        return [
            self::META_QUERY_KEY => [
                'key' => $config->getDateSource(),
                'value' => $value,
                'compare' => $compare,
                'type' => 'DATETIME',
            ],
        ];
    }
}
