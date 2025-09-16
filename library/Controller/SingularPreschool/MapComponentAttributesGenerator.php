<?php

namespace Municipio\Controller\SingularPreschool;

use Municipio\Schema\Preschool;

class MapComponentAttributesGenerator
{
    public function __construct(private Preschool $preschool)
    {
    }

    public function generate(): mixed
    {
        $latitude  = $this->preschool->getProperty('latitude');
        $longitude = $this->preschool->getProperty('longitude');

        if (empty($latitude) || empty($longitude)) {
            return null;
        }

        return [
            'pins'          => [ [ 'lat' => $latitude, 'lng' => $longitude ] ],
            'startPosition' => [ 'lat' => $latitude, 'lng' => $longitude, 'zoom' => 14 ],
        ];
    }
}
