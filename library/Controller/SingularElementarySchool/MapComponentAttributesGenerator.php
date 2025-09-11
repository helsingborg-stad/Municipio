<?php

namespace Municipio\Controller\SingularElementarySchool;

use Municipio\Schema\ElementarySchool;

class MapComponentAttributesGenerator
{
    public function __construct(private ElementarySchool $elementarySchool)
    {
    }

    public function generate(): mixed
    {
        $latitude  = $this->elementarySchool->getProperty('latitude');
        $longitude = $this->elementarySchool->getProperty('longitude');

        if (empty($latitude) || empty($longitude)) {
            return null;
        }

        return [
            'pins'          => [ [ 'lat' => $latitude, 'lng' => $longitude ] ],
            'startPosition' => [ 'lat' => $latitude, 'lng' => $longitude, 'zoom' => 14 ],
        ];
    }
}
