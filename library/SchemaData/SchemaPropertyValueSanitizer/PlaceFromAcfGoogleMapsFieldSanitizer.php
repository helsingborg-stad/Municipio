<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

/**
 * PlaceFromAcfGoogleMapsFieldSanitizer
 */
class PlaceFromAcfGoogleMapsFieldSanitizer implements SchemaPropertyValueSanitizerInterface
{
    private mixed $value;
    private array $allowedTypes;

    /**
     * Constructor for the PlaceFromAcfGoogleMapsFieldSanitizer class.
     *
     * @param SchemaPropertyValueSanitizerInterface $inner The inner sanitizer to delegate to if the value is not a GeoCoordinates.
     */
    public function __construct(private $inner = new NullSanitizer())
    {
    }

    /**
     * Sanitizes the given value based on the allowed types.
     *
     * @param mixed $value The value to sanitize.
     * @param array $allowedTypes The allowed types for the value.
     *
     * @return mixed The sanitized value.
     */
    public function sanitize(mixed $value, array $allowedTypes): mixed
    {
        $this->value        = $value;
        $this->allowedTypes = $allowedTypes;

        if ($this->shouldSanitize()) {
            return $this->getGeoCoordinatesFromValue();
        }

        if (is_array($value) && in_array('Place[]', $allowedTypes)) {
            return array_map(function ($value) {
                return $this->sanitize($value, ['Place']);
            }, $value);
        }

        return $this->inner->sanitize($value, $allowedTypes);
    }

    /**
     * Determines if the value should be sanitized to a Place object.
     *
     * @return bool True if the value should be sanitized, false otherwise.
     */
    private function shouldSanitize(): bool
    {
        return
            in_array('Place', $this->allowedTypes) &&
            is_array($this->value) &&
            isset($this->value['lat']) &&
            isset($this->value['lng']);
    }

    /**
     * Converts the value to a GeoCoordinates object.
     *
     * @return \Municipio\Schema\Place The GeoCoordinates object.
     */
    private function getGeoCoordinatesFromValue(): \Municipio\Schema\Place
    {
        $postalAddress = new \Municipio\Schema\Place();

        $postalAddress['latitude']       = $this->value['lat'];
        $postalAddress['longitude']      = $this->value['lng'];
        $postalAddress['name']           = $this->value['name'] ?? null;
        $postalAddress['address']        = $this->value['address'] ?? null;
        $postalAddress['postalCode']     = $this->value['post_code'] ?? null;
        $postalAddress['addressCountry'] = $this->value['country'] ?? null;

        return $postalAddress;
    }
}
