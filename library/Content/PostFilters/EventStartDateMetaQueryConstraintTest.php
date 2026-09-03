<?php

namespace Municipio\Content\PostFilters;

use Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs\MetaQueryKeys;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class EventStartDateMetaQueryConstraintTest extends TestCase
{
    #[TestDox('combines the expiration constraint with the selected date interval')]
    public function testApplyCombinesWithDateInterval(): void
    {
        $metaQuery = (new EventStartDateMetaQueryConstraint())->apply([
            MetaQueryKeys::DATE_CLAUSE => [
                'key'     => 'startDate',
                'value'   => ['2000-01-01 00:00:00', '2099-12-31 23:59:59'],
                'compare' => 'BETWEEN',
                'type'    => 'DATETIME',
            ],
            'event_tags' => [
                'key'   => 'event_tags',
                'value' => 'music',
            ],
        ], '2026-09-02 12:00:00');

        $this->assertArrayNotHasKey('municipio_event_not_expired', $metaQuery);
        $this->assertSame(['2026-09-02 12:00:00', '2099-12-31 23:59:59'], $metaQuery[MetaQueryKeys::DATE_CLAUSE]['value']);
        $this->assertSame('music', $metaQuery['event_tags']['value']);
    }

    #[TestDox('combines the expiration constraint with an end-date-only interval')]
    public function testApplyCombinesWithEndDateOnlyInterval(): void
    {
        $metaQuery = (new EventStartDateMetaQueryConstraint())->apply([
            MetaQueryKeys::DATE_CLAUSE => [
                'key'     => 'startDate',
                'value'   => '2099-12-31 23:59:59',
                'compare' => '<=',
                'type'    => 'DATETIME',
            ],
        ], '2026-09-02 12:00:00');

        $this->assertSame('BETWEEN', $metaQuery[MetaQueryKeys::DATE_CLAUSE]['compare']);
        $this->assertSame(['2026-09-02 12:00:00', '2099-12-31 23:59:59'], $metaQuery[MetaQueryKeys::DATE_CLAUSE]['value']);
    }

    #[TestDox('keeps a selected future start date')]
    public function testApplyKeepsFutureStartDate(): void
    {
        $metaQuery = (new EventStartDateMetaQueryConstraint())->apply([
            MetaQueryKeys::DATE_CLAUSE => [
                'key'     => 'startDate',
                'value'   => '2026-10-01 00:00:00',
                'compare' => '>=',
                'type'    => 'DATETIME',
            ],
        ], '2026-09-02 12:00:00');

        $this->assertSame('2026-10-01 00:00:00', $metaQuery[MetaQueryKeys::DATE_CLAUSE]['value']);
    }
}
