<?php

namespace Municipio\Controller\SingularPlace;

use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Helper\WpService;
use Municipio\PostObject\PostObjectInterface;

class GetPlaceActions
{
    public static function getPlaceActions(PostObjectInterface $place): array
    {
        $tourBookingPage = self::createTourBookingPageAction($place);
        return $tourBookingPage;
    }

    private static function createTourBookingPageAction(PostObjectInterface $place): array
    {
        $wpService = WpService::get();
        $tourBookingPage = EnsureArrayOf::ensureArrayOf($place->getSchemaProperty('tourBookingPage'), 'string');

        $formattedTourBookingPages = [];
        foreach ($tourBookingPage as $index => $bookingLink) {
            $formattedTourBookingPages[] = [
                'text' => $wpService->__('Book here', 'municipio'),
                'href' => $bookingLink,
                'color' => 'primary',
                'style' => 'filled',
                'classList' => ['u-width--100'],
            ];
        }

        return $formattedTourBookingPages;
    }
}