<?php

namespace Municipio\SchemaData\Utils\OpeningHoursSpecificationToString;

use Municipio\Schema\DayOfWeek;
use Municipio\Schema\OpeningHoursSpecification;
use WpService\Contracts\__;
use WpService\Contracts\_x;

/**
 * Converts OpeningHoursSpecification objects to array of string representations.
 */
class OpeningHoursSpecificationToString implements OpeningHoursSpecificationToStringInterface
{
    /**
     * Constructor.
     */
    public function __construct(private _x&__ $wpService)
    {
    }

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

        $opens       = $this->formatTime($opens);
        $closes      = $this->formatTime($closes);
        $closedLabel = $this->wpService->_x('closed', 'Schema OpeningHoursSpecification', 'municipio');
        $time        = empty($opens) && empty($closes) ? $closedLabel : sprintf('%s-%s', $opens, $closes);

        // Handle custom days (e.g., holidays) using 'name' property
        if ($name) {
            return [sprintf('%s: %s', $name, $time)];
        }

        if (is_array($days)) {
            return $this->formatDays($days, $time);
        }

        return [$this->formatDay($days, $time)];
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
        return $this->getDayMap()[$dayUrl] ?? $dayUrl;
    }

    /**
     * Get day map from schema.org URLs to human-readable names.
     *
     * @return array<string>
     */
    private function getDayMap(): array
    {
        return [
            DayOfWeek::Monday    => $this->wpService->__('Monday', 'municipio'),
            DayOfWeek::Tuesday   => $this->wpService->__('Tuesday', 'municipio'),
            DayOfWeek::Wednesday => $this->wpService->__('Wednesday', 'municipio'),
            DayOfWeek::Thursday  => $this->wpService->__('Thursday', 'municipio'),
            DayOfWeek::Friday    => $this->wpService->__('Friday', 'municipio'),
            DayOfWeek::Saturday  => $this->wpService->__('Saturday', 'municipio'),
            DayOfWeek::Sunday    => $this->wpService->__('Sunday', 'municipio'),
        ];
    }

    /**
     * Get human-readable day names.
     *
     * @return array<string>
     */
    private function getDayNames(): array
    {
        return [
            $this->wpService->__('Monday', 'municipio'),
            $this->wpService->__('Tuesday', 'municipio'),
            $this->wpService->__('Wednesday', 'municipio'),
            $this->wpService->__('Thursday', 'municipio'),
            $this->wpService->__('Friday', 'municipio'),
            $this->wpService->__('Saturday', 'municipio'),
            $this->wpService->__('Sunday', 'municipio'),
        ];
    }

    /**
     * Format multiple days.
     *
     * @param array $days
     * @param string $time
     * @return array
     */
    private function formatDays(array $days, string $time): array
    {
        $result     = [];
        $dayIndexes = array_map(
            fn($d) => array_search($this->getDayName($d), $this->getDayNames()),
            $days
        );
        sort($dayIndexes);

        if ($this->areDaysConsecutive($dayIndexes) && count($days) > 1) {
            $firstDay = $this->getDayName($days[0]);
            $lastDay  = $this->getDayName($days[count($days) - 1]);
            $result[] = sprintf('%s-%s: %s', $firstDay, $lastDay, $time);
        } elseif (count($days) === 1) {
            $result[] = $this->formatDay($days[0], $time);
        } else {
            foreach ($days as $day) {
                $result[] = $this->formatDay($day, $time);
            }
        }
        return $result;
    }

    /**
     * Format a single day.
     *
     * @param string $day
     * @param string $time
     * @return string
     */
    private function formatDay(string $day, string $time): string
    {
        return sprintf('%s: %s', $this->getDayName($day), $time);
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
