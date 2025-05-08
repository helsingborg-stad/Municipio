<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

use Municipio\Schema\GeoCoordinates;
use Municipio\Schema\Place;

class GoogleMapField extends AbstractField implements FieldInterface
{
    public function toArray(): array
    {
        return [
            'type'  => 'google_map',
            'key'   => $this->getKey(),
            'name'  => $this->name,
            'label' => $this->label
        ];
    }

    public function getValue(): mixed
    {
        if (!is_a($this->value, GeoCoordinates::class) && !is_a($this->value, Place::class)) {
            return [];
        }

        if (!$this->value->getProperty('latitude') || !$this->value->getProperty('longitude') || !$this->value->getProperty('address')) {
            return [];
        }

        return [
            'lat'     => $this->value->getProperty('latitude'),
            'lng'     => $this->value->getProperty('longitude'),
            'address' => $this->value->getProperty('address'),
        ];
    }
}
