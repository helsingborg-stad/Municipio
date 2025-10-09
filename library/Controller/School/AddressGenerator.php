<?php

namespace Municipio\Controller\School;

use Municipio\Schema\ElementarySchool;
use Municipio\Schema\Place;
use Municipio\Schema\Preschool;
use WpService\Contracts\_x;

class AddressGenerator implements ViewDataGeneratorInterface
{
    public function __construct(private ElementarySchool|Preschool $school, private _x $wpService)
    {
    }

    public function generate(): mixed
    {
        $places = $this->ensureArrayOfPlaces($this->school->getProperty('location'));

        if (empty($places)) {
            return null;
        }

        return array_map(fn($place) => $this->mapPlaceToAddress($place), $places);
    }

    private function ensureArrayOfPlaces(mixed $location): array
    {
        if (!is_array($location)) {
            $location = [ $location ];
        }

        return array_filter($location, fn($item) => is_a($item, Place::class) && !empty($item->getProperty('address')));
    }

    private function mapPlaceToAddress(Place $place): array
    {
        return [
            'address'        => $this->getAddress($place),
            'directionsLink' => $this->getDirectionsLinkAttributes($place),
        ];
    }

    private function getAddress(Place $place): ?string
    {
        $address = $place->getProperty('address');

        return is_string($address) && !empty($address)
            ? $address
            : null;
    }

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
