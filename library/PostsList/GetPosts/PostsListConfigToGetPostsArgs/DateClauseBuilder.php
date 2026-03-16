<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/**
 * Utility for creating date clauses in meta queries
 */
class DateClauseBuilder
{
    /**
     * Determine if a date meta query should be applied for the given field
     *
     * @param string $dateField
     * @return bool
     */
    public static function shouldApplyDateMetaQuery(string $dateField): bool
    {
        $invalidDateSourceValues = ['post_date', 'post_modified', 'none', null, ''];
        return !in_array($dateField, $invalidDateSourceValues, true);
    }

    /**
     * Build date meta query clause
     *
     * @param GetPostsConfigInterface $config
     * @return array
     */
    public static function buildDateMetaQueryClause(GetPostsConfigInterface $config): array
    {
        $dateSource = $config->getDateSource();
        
        if (!self::shouldApplyDateMetaQuery($dateSource)) {
            return [];
        }

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
            'key' => $dateSource,
            'value' => $value,
            'compare' => $compare,
            'type' => 'DATETIME',
        ];
    }

    /**
     * Ensure date clause exists in meta query for ordering purposes
     *
     * @param array $args Current query args
     * @param GetPostsConfigInterface $config
     * @return array Updated args with date clause if needed
     */
    public static function ensureDateClauseForOrdering(array $args, GetPostsConfigInterface $config): array
    {
        $dateSource = $config->getDateSource();
        
        // Only ensure clause for custom fields that need meta queries
        if (!self::shouldApplyDateMetaQuery($dateSource)) {
            return $args;
        }

        // Check if meta_query already has our date clause
        if (isset($args['meta_query'][MetaQueryKeys::DATE_CLAUSE])) {
            return $args; // Already exists
        }

        // Build the date clause
        $dateClause = self::buildDateMetaQueryClause($config);
        
        if (empty($dateClause)) {
            return $args;
        }

        // Ensure meta_query array exists and add our clause
        if (!isset($args['meta_query'])) {
            $args['meta_query'] = [];
        }

        $args['meta_query'][MetaQueryKeys::DATE_CLAUSE] = $dateClause;

        return $args;
    }
}