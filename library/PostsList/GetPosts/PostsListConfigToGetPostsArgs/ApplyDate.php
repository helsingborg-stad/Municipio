<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/*
 * Apply date from posts list config to get posts args
 */
class ApplyDate implements ApplyPostsListConfigToGetPostsArgsInterface
{
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
            ...$this->getDateOrMetaQuery($config)
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
            return ['meta_query' => [$this->buildMetaQuery($config)]];
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

        return ($config->getDateFrom() || $config->getDateTo())
            && in_array($column, ['post_date', 'post_modified']);
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
        return $query;
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
        return !in_array($column, ['post_date', 'post_modified'])
            && ($config->getDateFrom() || $config->getDateTo());
    }

    /**
     * Build the meta_query array
     *
     * @param GetPostsConfigInterface $config
     * @return array
     */
    private function buildMetaQuery(GetPostsConfigInterface $config): array
    {
        $dateFrom = $config->getDateFrom();
        $dateTo   = $config->getDateTo();
        $value    = $dateFrom && $dateTo ? [$dateFrom, $dateTo] : ($dateFrom ?: $dateTo);
        $compare  = ($dateFrom && $dateTo) ? 'BETWEEN' : ($dateFrom ? '>=' : '<=');

        return [
            'key'     => $config->getDateSource(),
            'value'   => $value,
            'compare' => $compare,
            'type'    => 'DATE'
        ];
    }
}
