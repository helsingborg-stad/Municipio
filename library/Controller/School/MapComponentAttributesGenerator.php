<?php

namespace Municipio\Controller\School;

use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Helper\EnsureArrayOf\EnsureArrayOf as EnsureArrayOfEnsureArrayOf;
use Municipio\Schema\ElementarySchool;
use Municipio\Schema\Place;
use Municipio\Schema\Preschool;

/**
 * Generates attributes for a map component based on school location data.
 */
class MapComponentAttributesGenerator
{
    /**
     * Constructor.
     *
     * @param ElementarySchool|Preschool $school The school entity.
     */
    public function __construct(private ElementarySchool|Preschool $school)
    {
    }

    /**
     * Generates the map component attributes including pins and start position.
     *
     * @return mixed Array of map attributes or null if no valid places found.
     */
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

    /**
     * Maps a Place object to a pin array for the map.
     *
     * @param Place $place The place to map.
     * @return array|null The pin data or null if coordinates are missing.
     */
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
     * Get the starting latitude by finding the center point between all places.
     *
     * @param array $places Array of Place objects.
     * @return float|null The calculated latitude or null.
     */
    private function getStartLatitude(array $places): ?float
    {
        $latitudes = array_map(fn($place) => $place->getProperty('latitude'), $places);
        return array_sum($latitudes) / count($latitudes);
    }

    /**
     * Get the starting longitude by finding the center point between all places.
     *
     * @param array $places Array of Place objects.
     * @return float|null The calculated longitude or null.
     */
    private function getStartLongitude(array $places): ?float
    {
        $longitudes = array_map(fn($place) => $place->getProperty('longitude'), $places);
        return array_sum($longitudes) / count($longitudes);
    }
}
