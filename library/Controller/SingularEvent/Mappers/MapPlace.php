<?php

namespace Municipio\Controller\SingularEvent\Mappers;

use Municipio\Schema\Event;
use Municipio\Schema\Place;

class MapPlace implements EventDataMapperInterface
{
    public function map(Event $event): array
    {
        $place = $event->getProperty('location') ?? [] ?: [];

        $place = array_filter($place, function ($item) {
            return is_a($item, Place::class);
        });

        if (empty($place)) {
            return $this->getEmptyResponse();
        }

        $place = $place[0];

        return [
            'url'     => $this->getUrl($place),
            'address' => $this->getAddress($place),
        ];
    }

    public function getUrl(Place $place): string
    {
        $placeName    = $place->getProperty('name') ?? $place->getProperty('address') ?? '';
        $placeAddress = $place->getProperty('address') ?? '';

        $googleMapsUrl = 'https://www.google.com/maps/search/?api=1&query=';
        $placeLink     = $googleMapsUrl . urlencode($placeName . ', ' . $placeAddress);

        return $placeLink;
    }

    private function getAddress(Place $place): ?string
    {
        return implode("<br>", [
            $place->getProperty('name') ?? null,
            $place->getProperty('address') ?? null,
        ]) ?: null;
    }

    private function getEmptyResponse(): array
    {
        return [
            'url'     => null,
            'address' => null,
        ];
    }
}
