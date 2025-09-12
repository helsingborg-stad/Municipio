<?php

namespace Municipio\Controller\SingularElementarySchool;

use Municipio\Schema\ElementarySchool;
use WpService\Contracts\_x;

class AddressGenerator implements ViewDataGeneratorInterface
{
    public function __construct(private ElementarySchool $elementarySchool, private _x $wpService)
    {
    }

    public function generate(): mixed
    {
        return [
            'address'        => $this->getAddress(),
            'directionsLink' => $this->getDirectionsLinkAttributes(),
        ];
    }

    private function getAddress(): ?string
    {
        $address = $this->elementarySchool->getProperty('address');

        return is_string($address) && !empty($address)
            ? $address
            : null;
    }

    private function getDirectionsLinkAttributes(): ?array
    {
        $address = $this->elementarySchool->getProperty('address');

        if (!is_string($address) || empty($address)) {
            return null;
        }

        return [
            'label' => $this->wpService->_x('Get directions', 'ElementarySchool', 'municipio'),
            'href'  => 'https://www.google.com/maps/dir//' . urlencode($address)
        ];
    }
}
