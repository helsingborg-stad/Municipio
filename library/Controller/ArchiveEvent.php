<?php

namespace Municipio\Controller;

use DateTimeInterface;
use Municipio\Helper\DateFormat;
use Municipio\PostObject\Decorators\AbstractPostObjectDecorator;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\Offer;
use Municipio\Schema\Place;
use Municipio\Schema\PriceSpecification;
use Municipio\Schema\Schedule;

/**
 * Class ArchiveEvent
 *
 * Handles archive for posts using the Event schema type.
 */
class ArchiveEvent extends \Municipio\Controller\Archive
{
    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->data['getEventPlaceName']  = fn(PostObjectInterface $post) => $this->getEventPlaceName($post);
        $this->data['getEventDate']       = fn(PostObjectInterface $post) => $this->getEventDate($post);
        $this->data['getDatebadgeDate']   = fn(PostObjectInterface $post) => $this->getDatebadgeDate($post);
        $this->data['getEventPriceRange'] = fn(PostObjectInterface $post) => $this->getEventPriceRange($post);
    }

    private function getEventPlaceName(PostObjectInterface $post): ?string
    {
        $locations     = $post->getSchemaProperty('location');
        $firstLocation = is_array($locations) ? reset($locations) : $locations;

        if (!is_a($firstLocation, Place::class)) {
            return null;
        }

        return
            $firstLocation->getProperty('name')
            ?: $firstLocation->getProperty('address')
            ?: null;
    }

    /**
     * Get the date of the first upcoming event occurrence
     */
    private function getEventDate(PostObjectInterface $post): ?string
    {
        $schedules             = $this->ensureArrayOf($post->getSchemaProperty('eventSchedule'), Schedule::class);
        $firstUpcomingDateTime = $this->getFirstUpcomingEventDateTimeFromArrayOfSchedules(...$schedules);
        $lastUpcomingDateTime  = $this->getLastUpcomingEventDateTimeFromArrayOfSchedules(...$schedules);

        if ($firstUpcomingDateTime === null) {
            return null;
        }

        $dateFormat = DateFormat::getDateFormat('date');

        if ($lastUpcomingDateTime !== null && $firstUpcomingDateTime->format('Y-m-d') !== $lastUpcomingDateTime->format('Y-m-d')) {
            return $firstUpcomingDateTime->format($dateFormat) . ' - ' . $lastUpcomingDateTime->format($dateFormat);
        }

        return $firstUpcomingDateTime->format($dateFormat);
    }

    private function getDatebadgeDate(PostObjectInterface $post): ?string
    {
        $schedules             = $this->ensureArrayOf($post->getSchemaProperty('eventSchedule'), Schedule::class);
        $firstUpcomingDateTime = $this->getFirstUpcomingEventDateTimeFromArrayOfSchedules(...$schedules);

        if ($firstUpcomingDateTime === null) {
            return null;
        }

        return $firstUpcomingDateTime->format(DateFormat::getDateFormat('Y-m-d'));
    }

    private function getFirstUpcomingEventDateTimeFromArrayOfSchedules(Schedule ...$schedules): ?DateTimeInterface
    {
        $firstUpcomingSchedule = $this->getFirstUpcomingScheduleFromArrayOfSchedules(...$schedules);

        if ($firstUpcomingSchedule === null) {
            return null;
        }

        return $firstUpcomingSchedule->getProperty('startDate') ?: null;
    }

    private function getLastUpcomingEventDateTimeFromArrayOfSchedules(Schedule ...$schedules): ?DateTimeInterface
    {
        $lastUpcomingSchedule = $this->getLastUpcomingScheduleFromArrayOfSchedules(...$schedules);

        if ($lastUpcomingSchedule === null) {
            return null;
        }

        return $lastUpcomingSchedule->getProperty('endDate') ?: null;
    }

    private function getFirstUpcomingScheduleFromArrayOfSchedules(Schedule ...$schedules): ?Schedule
    {
        usort($schedules, fn(Schedule $a, Schedule $b) => $a->getProperty('startDate') <=> $b->getProperty('startDate'));
        foreach ($schedules as $schedule) {
            if ($schedule->getProperty('startDate') >= date('c')) {
                return $schedule;
            }
        }

        return null;
    }

    private function getLastUpcomingScheduleFromArrayOfSchedules(Schedule ...$schedules): ?Schedule
    {
        usort($schedules, fn(Schedule $a, Schedule $b) => $b->getProperty('endDate') <=> $a->getProperty('endDate'));
        foreach ($schedules as $schedule) {
            if ($schedule->getProperty('endDate') >= date('c')) {
                return $schedule;
            }
        }

        return null;
    }

    private function ensureArrayOf($value, $ensuredType): array
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        return array_filter($value, fn($item) => is_a($item, $ensuredType));
    }

    private function getEventPriceRange(PostObjectInterface $post): ?string
    {
        static $staticCache = [];

        if (isset($staticCache[$post->getId()])) {
            return $staticCache[$post->getId()];
        }

        $offers              = $this->ensureArrayOf($post->getSchemaProperty('offers'), Offer::class);
        $priceSpecifications = array_map(fn($offer) => $offer->getProperty('priceSpecification'), $offers);
        $priceSpecifications = array_merge(...array_map(fn($spec) => $this->ensureArrayOf($spec, PriceSpecification::class), $priceSpecifications));
        $prices              = array_map((fn(PriceSpecification $spec) => $spec->getProperty('price')), $priceSpecifications);
        $prices              = array_filter(array_map(fn($price) => is_numeric($price) ? floatval($price) : null, $prices));

        $minPrice = !empty($prices) ? min($prices) : null;
        $maxPrice = !empty($prices) ? max($prices) : null;
        $currency = !empty($priceSpecifications) ? $priceSpecifications[0]->getProperty('priceCurrency') : '';

        if ($currency === 'SEK') {
            $currency = 'kr';
        }

        // return range if we found any prices from price specifications
        if (is_numeric($minPrice) && is_numeric($maxPrice)) {
            if ($minPrice === $maxPrice) {
                return $staticCache[$post->getId()] ??= number_format($minPrice, 0, ',', ' ') . ' ' . $currency;
            }

            return $staticCache[$post->getId()] ??= number_format($minPrice, 0, ',', ' ') . '-' . number_format($maxPrice, 0, ',', ' ') . ' ' . $currency;
        }

        return $staticCache[$post->getId()] ??= null;
    }
}
