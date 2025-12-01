<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

use Municipio\Schema\GeoCoordinates;
use Municipio\Schema\Place;

/**
 * Class GoogleMapField
 *
 * This class is responsible for creating a Google Map field.
 */
class GoogleMapField extends AbstractField implements FieldInterface
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type'  => 'google_map',
            'key'   => $this->getKey(),
            'name'  => $this->getName(),
            'label' => $this->getLabel(),
        ];
    }

    /**
     * @inheritDoc
     */
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
