<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\MetaDataFromSchema\Mappers;

use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schedule;
use Municipio\SchemaData\ExternalContent\SyncHandler\MetaDataFromSchema\MetaDataItemInterface;

class MapEventStartDates implements MetaDataItemMapperInterface
{
    /**
     * @inheritDoc
     */
    public function map(BaseType $schema): \Generator
    {
        if ($schema->getType() !== 'Event') {
            return;
        }

        $schedules = $schema->getProperty('eventSchedule');
        $schedules = EnsureArrayOf::ensureArrayOf($schedules, Schedule::class);
        $schedules = array_filter($schedules, [$this, 'scheduleHasStartDate']);
        $schedules = array_map([$this, 'convertStartDateToString'], $schedules);

        foreach ($schedules as $schedule) {
            yield self::createMetaDataItem($schedule->getProperty('startDate'));
        }

        return;
    }

    private function scheduleHasStartDate(Schedule $schedule): bool
    {
        return $schedule->getProperty('startDate') !== null;
    }

    private function convertStartDateToString(Schedule $schedule): Schedule
    {
        $startDate = $schedule->getProperty('startDate');

        if ($startDate instanceof \DateTime) {
            $schedule->setProperty('startDate', $startDate->format(\DateTime::ATOM));
        }

        if (is_string($startDate)) {
            // Validate string format
            $time = @strtotime($startDate) ?? false;

            if ($time !== false) {
                $schedule->setProperty('startDate', date(\DateTime::ATOM, $time));
            }
        }

        return $schedule;
    }

    private static function createMetaDataItem(string $startDate): MetaDataItemInterface
    {
        return new class($startDate) implements MetaDataItemInterface {
            public function __construct(
                private string $startDate,
            ) {}

            public function getKey(): string
            {
                return 'startDate';
            }

            public function getValue(): mixed
            {
                return $this->startDate;
            }
        };
    }
}
