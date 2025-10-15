<?php

namespace Municipio\Controller\School;

use Municipio\Controller\ArchiveEvent\EnsureArrayOf;
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
        $places = EnsureArrayOf::ensureArrayOf($this->school->getProperty('location'), Place::class);
        $places = array_filter($places, fn($place) => !empty($place->getProperty('address')));

        if (empty($places)) {
            return null;
        }

        return array_map(fn($place) => $this->mapPlaceToAddress($place), $places);
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
