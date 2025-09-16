<?php

namespace Municipio\Controller\School;

use Municipio\Schema\ElementarySchool;
use Municipio\Schema\Preschool;

class MapComponentAttributesGenerator
{
    public function __construct(private ElementarySchool|Preschool $school)
    {
    }

    public function generate(): mixed
    {
        $latitude  = $this->school->getProperty('latitude');
        $longitude = $this->school->getProperty('longitude');

        if (empty($latitude) || empty($longitude)) {
            return null;
        }

        return [
            'pins'          => [ [ 'lat' => $latitude, 'lng' => $longitude ] ],
            'startPosition' => [ 'lat' => $latitude, 'lng' => $longitude, 'zoom' => 14 ],
        ];
    }
}
