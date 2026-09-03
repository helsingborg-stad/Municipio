<?php

namespace Municipio\Content\PostFilters;

use Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs\MetaQueryKeys;

/**
 * Adds the "not expired" constraint to an event start-date meta query.
 */
class EventStartDateMetaQueryConstraint
{
    private const EVENT_START_DATE_META_KEY = 'startDate';

    private const EVENT_EXPIRATION_CLAUSE = 'municipio_event_not_expired';

    /**
     * @param array $metaQuery
     * @param string $currentDate MySQL datetime.
     * @return array
     */
    public function apply(array $metaQuery, string $currentDate): array
    {
        $dateClause = $metaQuery[MetaQueryKeys::DATE_CLAUSE] ?? null;

        if (is_array($dateClause) && ($dateClause['key'] ?? null) === self::EVENT_START_DATE_META_KEY) {
            $metaQuery[MetaQueryKeys::DATE_CLAUSE] = $this->strengthenLowerBound($dateClause, $currentDate);
            return $metaQuery;
        }

        // A query without PostsList's startDate range still needs the same
        // protection. The named key keeps this operation idempotent.
        $metaQuery[self::EVENT_EXPIRATION_CLAUSE] = [
            'key'     => self::EVENT_START_DATE_META_KEY,
            'value'   => $currentDate,
            'compare' => '>=',
            'type'    => 'DATETIME',
        ];

        return $metaQuery;
    }

    /**
     * Combine an existing startDate range with the expiration lower bound.
     *
     * @param array $dateClause
     * @param string $currentDate
     * @return array
     */
    private function strengthenLowerBound(array $dateClause, string $currentDate): array
    {
        $compare = strtoupper($dateClause['compare'] ?? '');

        if ($compare === 'BETWEEN' && is_array($dateClause['value'] ?? null)) {
            $dateClause['value'][0] = $this->latestDate($dateClause['value'][0] ?? null, $currentDate);
            return $dateClause;
        }

        if (in_array($compare, ['>=', '>'], true)) {
            $dateClause['value'] = $this->latestDate($dateClause['value'] ?? null, $currentDate);
            return $dateClause;
        }

        if (in_array($compare, ['<=', '<'], true)) {
            $dateClause['compare'] = 'BETWEEN';
            $dateClause['value'] = [$currentDate, $dateClause['value']];
        }

        return $dateClause;
    }

    /**
     * Return the later of two MySQL datetime values.
     */
    private function latestDate(mixed $date, string $currentDate): string
    {
        return is_string($date) && $date > $currentDate ? $date : $currentDate;
    }
}
