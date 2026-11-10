<?php

declare(strict_types=1);

namespace Modularity\Module\Posts\Helper;

use DateTimeImmutable;
use Municipio\PostObject\NullPostObject;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

/**
 * Tests for event start timestamp resolution.
 */
class EventStartTimestampResolverTest extends TestCase
{
    /**
     * It returns null when post schema is not an event.
     */
    #[TestDox('returns null when schema is not Event')]
    public function testReturnsNullWhenSchemaIsNotEvent(): void
    {
        $post = $this->createPostWithSchema(Schema::article());

        $resolver = new EventStartTimestampResolver('2026-01-10 12:00:00');

        static::assertNull($resolver->getTimestamp($post));
    }

    /**
     * It returns null when event has no schedules.
     */
    #[TestDox('returns null when event has no schedules')]
    public function testReturnsNullWhenEventHasNoSchedules(): void
    {
        $post = $this->createPostWithSchema(Schema::event()->eventSchedule([]));

        $resolver = new EventStartTimestampResolver('2026-01-10 12:00:00');

        static::assertNull($resolver->getTimestamp($post));
    }

    /**
     * It returns the first upcoming schedule timestamp when both past and future schedules exist.
     */
    #[TestDox('returns first upcoming schedule timestamp when both past and future schedules exist')]
    public function testReturnsFirstUpcomingScheduleTimestamp(): void
    {
        $pastDate = new DateTimeImmutable('2026-01-09 10:00:00');
        $upcomingSoonerDate = new DateTimeImmutable('2026-01-10 13:00:00');
        $upcomingLaterDate = new DateTimeImmutable('2026-01-11 10:00:00');

        $post = $this->createPostWithSchema(
            Schema::event()->eventSchedule([
                Schema::schedule()->startDate($upcomingLaterDate),
                Schema::schedule()->startDate($pastDate),
                Schema::schedule()->startDate($upcomingSoonerDate),
            ]),
        );

        $resolver = new EventStartTimestampResolver('2026-01-10 12:00:00');

        static::assertSame($upcomingSoonerDate->getTimestamp(), $resolver->getTimestamp($post));
    }

    /**
     * It returns the closest passed schedule timestamp when no upcoming schedules exist.
     */
    #[TestDox('returns closest passed schedule timestamp when no upcoming schedules exist')]
    public function testReturnsClosestPassedScheduleTimestampWhenNoUpcomingSchedulesExist(): void
    {
        $olderPassedDate = new DateTimeImmutable('2026-01-08 10:00:00');
        $closestPassedDate = new DateTimeImmutable('2026-01-10 11:59:59');

        $post = $this->createPostWithSchema(
            Schema::event()->eventSchedule([
                Schema::schedule()->startDate($olderPassedDate),
                Schema::schedule()->startDate($closestPassedDate),
            ]),
        );

        $resolver = new EventStartTimestampResolver('2026-01-10 12:00:00');

        static::assertSame($closestPassedDate->getTimestamp(), $resolver->getTimestamp($post));
    }

    /**
     * It resolves first upcoming schedule relative to supplied reference date.
     */
    #[TestDox('respects supplied dateFrom when selecting upcoming schedule')]
    public function testRespectsSuppliedDateFromWhenSelectingUpcomingSchedule(): void
    {
        $referenceDate = '2026-01-15 12:00:00';
        $beforeReference = new DateTimeImmutable('2026-01-14 12:00:00');
        $afterReference = new DateTimeImmutable('2026-01-16 09:00:00');

        $post = $this->createPostWithSchema(
            Schema::event()->eventSchedule([
                Schema::schedule()->startDate($beforeReference),
                Schema::schedule()->startDate($afterReference),
            ]),
        );

        $resolver = new EventStartTimestampResolver($referenceDate);

        static::assertSame($afterReference->getTimestamp(), $resolver->getTimestamp($post));
    }

    /**
     * Creates a post fixture exposing a specific schema.
     */
    private function createPostWithSchema(BaseType $schema): PostObjectInterface
    {
        return new class($schema) extends NullPostObject {
            public function __construct(
                private BaseType $schema,
            ) {}

            public function getSchema(): BaseType
            {
                return $this->schema;
            }
        };
    }
}