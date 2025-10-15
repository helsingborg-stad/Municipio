<?php

namespace Municipio\Controller\School;

use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Helper\EnsureArrayOf\EnsureArrayOf as EnsureArrayOfEnsureArrayOf;
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
        $places = EnsureArrayOfEnsureArrayOf::ensureArrayOf($this->school->getProperty('location'), Place::class);
        $places = array_filter($places, fn($place) => !empty($place->getProperty('latitude')) && !empty($place->getProperty('longitude')));

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
