<?php

namespace Municipio\SchemaData\Utils\OpeningHoursSpecificationToString;

use Municipio\Schema\DayOfWeek;
use Municipio\Schema\OpeningHoursSpecification;

/**
 * Converts OpeningHoursSpecification objects to array of string representations.
 */
class OpeningHoursSpecificationToString implements OpeningHoursSpecificationToStringInterface
{
    private const DAY_MAP = [
        DayOfWeek::Monday    => 'Monday',
        DayOfWeek::Tuesday   => 'Tuesday',
        DayOfWeek::Wednesday => 'Wednesday',
        DayOfWeek::Thursday  => 'Thursday',
        DayOfWeek::Friday    => 'Friday',
        DayOfWeek::Saturday  => 'Saturday',
        DayOfWeek::Sunday    => 'Sunday',
    ];

    private const DAY_NAMES = [
        'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'
    ];

    /**
     * Convert OpeningHoursSpecification to array of string representations.
     *
     * @param OpeningHoursSpecification $openingHours
     * @return string[]
     */
    public function convert(OpeningHoursSpecification $openingHours): array
    {
        $days   = $openingHours->getProperty('dayOfWeek') ?? null;
        $name   = $openingHours->getProperty('name') ?? null;
        $opens  = $openingHours->getProperty('opens') ?? '';
        $closes = $openingHours->getProperty('closes') ?? '';

        $opens  = $this->formatTime($opens);
        $closes = $this->formatTime($closes);

        // Handle custom days (e.g., holidays) using 'name' property
        if ($name) {
            return [sprintf('%s: %s-%s', $name, $opens, $closes)];
        }

        if (is_array($days)) {
            return $this->formatDays($days, $opens, $closes);
        }

        return [$this->formatDay($days, $opens, $closes)];
    }

    /**
     * Format time string to HH:MM.
     *
     * @param string $time
     * @return string
     */
    private function formatTime(string $time): string
    {
        return substr($time, 0, 5);
    }

    /**
     * Get day name from schema.org URL.
     *
     * @param string $dayUrl
     * @return string
     */
    private function getDayName(string $dayUrl): string
    {
        return self::DAY_MAP[$dayUrl] ?? $dayUrl;
    }

    /**
     * Format multiple days.
     *
     * @param array $days
     * @param string $opens
     * @param string $closes
     * @return array
     */
    private function formatDays(array $days, string $opens, string $closes): array
    {
        $result     = [];
        $dayIndexes = array_map(
            fn($d) => array_search($this->getDayName($d), self::DAY_NAMES),
            $days
        );
        sort($dayIndexes);

        if ($this->areDaysConsecutive($dayIndexes) && count($days) > 1) {
            $firstDay = $this->getDayName($days[0]);
            $lastDay  = $this->getDayName($days[count($days) - 1]);
            $result[] = sprintf('%s-%s: %s-%s', $firstDay, $lastDay, $opens, $closes);
        } elseif (count($days) === 1) {
            $result[] = $this->formatDay($days[0], $opens, $closes);
        } else {
            foreach ($days as $day) {
                $result[] = $this->formatDay($day, $opens, $closes);
            }
        }
        return $result;
    }

    /**
     * Format a single day.
     *
     * @param string $day
     * @param string $opens
     * @param string $closes
     * @return string
     */
    private function formatDay(string $day, string $opens, string $closes): string
    {
        return sprintf('%s: %s-%s', $this->getDayName($day), $opens, $closes);
    }

    /**
     * Check if day indexes are consecutive.
     *
     * @param array $indexes
     * @return bool
     */
    private function areDaysConsecutive(array $indexes): bool
    {
        for ($i = 1, $len = count($indexes); $i < $len; $i++) {
            if ($indexes[$i] !== $indexes[$i - 1] + 1) {
                return false;
            }
        }
        return true;
    }
}
