<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

class GeoCoordinatesFromAcfGoogleMapsFieldSanitizer implements SchemaPropertyValueSanitizer
{
    private mixed $value;
    private array $allowedTypes;

    public function __construct(private $inner = new NullSanitizer())
    {
    }

    public function sanitize(mixed $value, array $allowedTypes): mixed
    {
        $this->value        = $value;
        $this->allowedTypes = $allowedTypes;

        if ($this->shouldSanitize()) {
            return $this->getGeoCoordinatesFromValue();
        }

        if (is_array($value) && in_array('GeoCoordinates[]', $allowedTypes)) {
            return array_map(function ($value) {
                return $this->sanitize($value, ['GeoCoordinates']);
            }, $value);
        }

        return $this->inner->sanitize($value, $allowedTypes);
    }

    private function shouldSanitize(): bool
    {
        return
            in_array('GeoCoordinates', $this->allowedTypes) &&
            is_array($this->value) &&
            isset($this->value['lat']) &&
            isset($this->value['lng']);
    }

    private function getGeoCoordinatesFromValue(): \Spatie\SchemaOrg\GeoCoordinates
    {
        $postalAddress = new \Spatie\SchemaOrg\GeoCoordinates();

        $postalAddress['latitude']       = $this->value['lat'];
        $postalAddress['longitude']      = $this->value['lng'];
        $postalAddress['name']           = $this->value['name'] ?? null;
        $postalAddress['address']        = $this->value['address'] ?? null;
        $postalAddress['postalCode']     = $this->value['post_code'] ?? null;
        $postalAddress['addressCountry'] = $this->value['country'] ?? null;

        return $postalAddress;
    }
}
