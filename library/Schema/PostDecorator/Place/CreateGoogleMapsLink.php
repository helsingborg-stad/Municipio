<?php

namespace Municipio\Schema\PostDecorator\Place;

class CreateGoogleMapsLink {
    public function __construct(private array $schemaData){}

    public function createGoogleMapsLink() 
    {
        echo '<pre>' . print_r( $this->schemaData, true ) . '</pre>';
        if (empty($this->schemaData['geo']['lat'] || empty($this->schemaData['geo']['lng']))) {
            return null;
        }

        
        return 
            'https://www.google.com/maps/dir/?api=1&destination=' .
            $this->schemaData['geo']['lat'] .
            ',' .
            $this->schemaData['geo']['lng'] . 
            '&travelmode=transit';
    }
}