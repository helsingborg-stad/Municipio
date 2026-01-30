<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use DateTime;
use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Schema\Event;
use Municipio\Schema\Schedule;
use Municipio\Schema\TextObject;
use WpService\Contracts\Wpautop;

/**
 * Maps the currently viewed schedule's description to formatted text.
 */
class MapScheduleDescription implements EventDataMapperInterface
{
    /**
     * Constructor
     *
     * @param Wpautop $wpService
     * @param DateTime|null $currentlyViewing The date/time currently being viewed
     */
    public function __construct(
        private Wpautop $wpService,
        private ?DateTime $currentlyViewing = null
    ) {
    }

    /**
     * Maps the current schedule's description to formatted text.
     *
     * @param Event $event
     * @return string
     */
    public function map(Event $event): string
    {
        if ($this->currentlyViewing === null) {
            return '';
        }

        $schedule = $this->findCurrentSchedule($event);

        if ($schedule === null) {
            return '';
        }

        return $this->extractDescription($schedule);
    }

    /**
     * Finds the schedule matching the currently viewed date/time.
     *
     * @param Event $event
     * @return Schedule|null
     */
    private function findCurrentSchedule(Event $event): ?Schedule
    {
        $schedules = EnsureArrayOf::ensureArrayOf(
            $event->getProperty('eventSchedule'),
            Schedule::class
        );

        foreach ($schedules as $schedule) {
            $startDate = $schedule->getProperty('startDate');

            if ($startDate && $startDate->getTimestamp() === $this->currentlyViewing->getTimestamp()) {
                return $schedule;
            }
        }

        return null;
    }

    /**
     * Extracts and formats the description from a schedule.
     *
     * @param Schedule $schedule
     * @return string
     */
    private function extractDescription(Schedule $schedule): string
    {
        $description = $schedule->getProperty('description');

        if (empty($description)) {
            return '';
        }

        if (!is_array($description)) {
            $description = [$description];
        }

        $descriptionText = implode(
            '',
            array_filter(
                array_map([self::class, 'sanitizeText'], $description)
            )
        );

        if (empty($descriptionText)) {
            return '';
        }

        return $this->wpService->wpautop($descriptionText);
    }

    /**
     * Sanitizes a text value or TextObject.
     *
     * @param mixed $text
     * @return string|null
     */
    private static function sanitizeText(mixed $text): ?string
    {
        if (is_string($text)) {
            return $text;
        } elseif (is_a($text, TextObject::class)) {
            return $text->getProperty('text') ?? null;
        }

        return null;
    }
}
