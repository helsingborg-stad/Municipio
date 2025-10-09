<?php

namespace Municipio\Controller\School;

use Municipio\Schema\ElementarySchool;
use Municipio\Schema\Place;
use Municipio\Schema\Preschool;

class MapComponentAttributesGenerator
{
    public function __construct(private ElementarySchool|Preschool $school)
    {
    }

    public function generate(): mixed
    {
        $places = $this->ensureArrayOfPlaces($this->school->getProperty('location'));

        if (empty($places)) {
            return null;
        }

        return [
            'pins'          => array_map(fn($place) => $this->mapPlaceToPin($place), $places),
            'startPosition' => [ 'lat' => $this->getStartLatitude($places), 'lng' => $this->getStartLongitude($places), 'zoom' => 14 ],
        ];
    }

    private function mapPlaceToPin(Place $place): ?array
    {
        $latitude  = $place->getProperty('latitude');
        $longitude = $place->getProperty('longitude');

        if (empty($latitude) || empty($longitude)) {
            return null;
        }

        return [
            'lat'     => $latitude,
            'lng'     => $longitude,
            'tooltip' => [
                'title' => $place->getProperty('address')
            ]
        ];
    }

    private function ensureArrayOfPlaces(mixed $location): array
    {
        if (!is_array($location)) {
            $location = [ $location ];
        }

        return array_filter($location, fn($item) => is_a($item, Place::class) && $item->getProperty('latitude') !== null && $item->getProperty('longitude') !== null);
    }


    /**
     * Get the starting latitude by finding the center point between all places
     */
    private function getStartLatitude(array $places): ?float
    {
        $latitudes = array_map(fn($place) => $place->getProperty('latitude'), $places);
        return array_sum($latitudes) / count($latitudes);
    }

    private function getStartLongitude(array $places): ?float
    {
        $longitudes = array_map(fn($place) => $place->getProperty('longitude'), $places);
        return array_sum($longitudes) / count($longitudes);
    }
}
