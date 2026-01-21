<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use DateTime;

class MapBookingLink implements EventDataMapperInterface
{
    public function __construct(
        private ?DateTime $currentScheduleDateTime = null,
    ) {}

    public function map(\Municipio\Schema\Event $event): ?string
    {
        $scheduleBookingLink = $this->getScheduleBookingLink($event);
        if ($scheduleBookingLink !== null) {
            return $scheduleBookingLink;
        }

        $offerBookingLink = $this->getFirstOfferBookingLink($event);
        if ($offerBookingLink !== null) {
            return $offerBookingLink;
        }

        return null;
    }

    private function getScheduleBookingLink(\Municipio\Schema\Event $event): ?string
    {
        $schedule = $this->findScheduleByDateTime($event);
        if ($schedule === null) {
            return null;
        }
        $url = $schedule->getProperty('url');
        return !empty($url) ? $url : null;
    }

    private function getFirstOfferBookingLink(\Municipio\Schema\Event $event): ?string
    {
        $offers = $event->getProperty('offers') ?? [];
        foreach ($offers as $offer) {
            $url = $offer->getProperty('url');
            if (!empty($url)) {
                return $url;
            }
        }
        return null;
    }

    private function findScheduleByDateTime(\Municipio\Schema\Event $event): ?\Municipio\Schema\Schedule
    {
        if ($this->currentScheduleDateTime === null) {
            return null;
        }
        $schedules = array_filter(
            $event->getProperty('eventSchedule') ?? [],
            fn(\Municipio\Schema\Schedule $schedule) => $schedule->getProperty('startDate')->getTimestamp() === $this->currentScheduleDateTime->getTimestamp(),
        );
        return !empty($schedules) ? array_shift($schedules) : null;
    }
}
