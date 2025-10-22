<?php

namespace Municipio\Controller\School;

use DateTime;
use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Schema\ElementarySchool;
use Municipio\Schema\Event;
use Municipio\Schema\Preschool;
use Municipio\Schema\Schedule;

class EventsGenerator implements ViewDataGeneratorInterface
{
    public function __construct(private ElementarySchool|Preschool $school)
    {
    }

    public function generate(): ?array
    {
        $events = $this->getEvents();

        if (empty($events)) {
            return null;
        }

        return $this->generateOccasions($events);
    }

    private function getEvents(): array
    {
        return EnsureArrayOf::ensureArrayOf(
            $this->school->getProperty('event'),
            Event::class
        );
    }

    private function generateOccasions(array $events): array
    {
        $occasions = [];

        foreach ($events as $event) {
            $schedules = $this->getSchedules($event);

            foreach ($schedules as $schedule) {
                $occasions[] = $this->createOccasion($event, $schedule);
            }
        }

        return $occasions;
    }

    private function getSchedules(Event $event): array
    {
        return EnsureArrayOf::ensureArrayOf(
            $event->getProperty('eventSchedule'),
            Schedule::class
        );
    }

    private function createOccasion(Event $event, Schedule $schedule): array
    {
        $startDate = $schedule->getProperty('startDate');
        $endDate = $schedule->getProperty('endDate');

        return [
            'name'             => $event->getProperty('name'),
            'timestamp'        => $startDate ? $this->getTimestampFromDateTime($startDate) : null,
            'startTimeEndTime' => $this->formatStartEndTime($startDate, $endDate),
            'description'      => $this->getDescription($event),
        ];
    }

    private function formatStartEndTime(?DateTime $startDate, ?DateTime $endDate): ?string
    {
        if ($startDate && $endDate) {
            return sprintf('%s - %s', $startDate->format('H:i'), $endDate->format('H:i'));
        }
        return null;
    }

    private function getTimestampFromDateTime(DateTime $dateTime): int
    {
        return $dateTime->getTimestamp();
    }

    private function getDescription(Event $event): string
    {
        $description = $event->getProperty('description');
        $descriptions = is_array($description) ? $description : [$description];
        $descriptions = array_filter($descriptions, 'is_string');

        return implode('', $descriptions);
    }
}
