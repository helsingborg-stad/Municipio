<?php

namespace Municipio\Controller\SingularPreschool;

use Municipio\Schema\Preschool;
use WpService\Contracts\_x;

class AddressGenerator implements ViewDataGeneratorInterface
{
    public function __construct(private Preschool $preschool, private _x $wpService)
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
        $address = $this->preschool->getProperty('address');

        return is_string($address) && !empty($address)
            ? $address
            : null;
    }

    private function getDirectionsLinkAttributes(): ?array
    {
        $address = $this->preschool->getProperty('address');

        if (!is_string($address) || empty($address)) {
            return null;
        }

        return [
            'label' => $this->wpService->_x('Get directions', 'ElementarySchool', 'municipio'),
            'href'  => 'https://www.google.com/maps/dir//' . urlencode($address)
        ];
    }
}
