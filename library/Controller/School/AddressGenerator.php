<?php

namespace Municipio\Controller\School;

use Municipio\Helper\EnsureArrayOf\EnsureArrayOf;
use Municipio\Schema\ElementarySchool;
use Municipio\Schema\Place;
use Municipio\Schema\Preschool;
use WpService\Contracts\_x;

/**
 * Generates address and directions data for a school.
 */
class AddressGenerator implements ViewDataGeneratorInterface
{
    /**
     * AddressGenerator constructor.
     *
     * @param ElementarySchool|Preschool $school   The school entity.
     * @param _x                         $wpService WordPress service for translations.
     */
    public function __construct(private ElementarySchool|Preschool $school, private _x $wpService)
    {
    }

    /**
     * Generates an array of address data for the school's locations.
     *
     * @return array|null Array of address data or null if none found.
     */
    public function generate(): mixed
    {
        $places = EnsureArrayOf::ensureArrayOf($this->school->getProperty('location'), Place::class);
        $places = array_filter($places, fn($place) => !empty($place->getProperty('address')));

        if (empty($places)) {
            return null;
        }

        return array_map(fn($place) => $this->mapPlaceToAddress($place), $places);
    }

    /**
     * Maps a Place object to an address array.
     *
     * @param Place $place The place to map.
     * @return array Address data for the place.
     */
    private function mapPlaceToAddress(Place $place): array
    {
        return [
            'address'        => $this->getAddress($place),
            'directionsLink' => $this->getDirectionsLinkAttributes($place),
        ];
    }

    /**
     * Retrieves the address string from a Place object.
     *
     * @param Place $place The place to get the address from.
     * @return string|null The address string or null if not available.
     */
    private function getAddress(Place $place): ?string
    {
        $address = $place->getProperty('address');

        return is_string($address) && !empty($address)
            ? $address
            : null;
    }

    /**
     * Generates the directions link attributes for a Place object.
     *
     * @param Place $place The place to generate the link for.
     * @return array|null Array with label and href, or null if address is missing.
     */
    private function getDirectionsLinkAttributes(Place $place): ?array
    {
        $address = $place->getProperty('address');

        if (!is_string($address) || empty($address)) {
            return null;
        }

        return [
            'label' => $this->wpService->_x('Get directions', 'ElementarySchool', 'municipio'),
            'href'  => 'https://www.google.com/maps/dir//' . urlencode($address)
        ];
    }
}
