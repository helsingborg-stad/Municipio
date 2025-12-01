<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

/**
 * Sanitizes the value of a Google Maps ACF field to a GeoCoordinates object.
 *
 * This class implements the SchemaPropertyValueSanitizerInterface interface and is responsible for
 * converting an array containing latitude and longitude into a GeoCoordinates object.
 */
class GeoCoordinatesFromAcfGoogleMapsFieldSanitizer implements SchemaPropertyValueSanitizerInterface
{
    private mixed $value;
    /**
     * @var array<string>
     */
    private array $allowedTypes;

    /**
     * Constructor for the GeoCoordinatesFromAcfGoogleMapsFieldSanitizer class.
     *
     * @param SchemaPropertyValueSanitizerInterface $inner The inner sanitizer to delegate to if the value is not a GeoCoordinates.
     */
    public function __construct(private SchemaPropertyValueSanitizerInterface $inner = new NullSanitizer())
    {
    }

    /**
     * Sanitizes the given value based on the allowed types.
     *
     * @param mixed $value The value to sanitize.
     * @param string[] $allowedTypes The allowed types for the value.
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

        if (is_array($value) && in_array('GeoCoordinates[]', $allowedTypes)) {
            return array_map(function ($value) {
                return $this->sanitize($value, ['GeoCoordinates']);
            }, $value);
        }

        return $this->inner->sanitize($value, $allowedTypes);
    }

    /**
     * Determines if the value should be sanitized to a GeoCoordinates object.
     *
     * @return bool True if the value should be sanitized, false otherwise.
     */
    private function shouldSanitize(): bool
    {
        return
            in_array('GeoCoordinates', $this->allowedTypes) &&
            is_array($this->value) &&
            isset($this->value['lat']) &&
            isset($this->value['lng']);
    }

    /**
     * Converts the value to a GeoCoordinates object.
     *
     * @return \Municipio\Schema\GeoCoordinates The GeoCoordinates object.
     */
    private function getGeoCoordinatesFromValue(): \Municipio\Schema\GeoCoordinates
    {
        $postalAddress = new \Municipio\Schema\GeoCoordinates();

        $postalAddress['latitude']       = $this->value['lat'];
        $postalAddress['longitude']      = $this->value['lng'];
        $postalAddress['name']           = $this->value['name'] ?? null;
        $postalAddress['address']        = $this->value['address'] ?? null;
        $postalAddress['postalCode']     = $this->value['post_code'] ?? null;
        $postalAddress['addressCountry'] = $this->value['country'] ?? null;

        return $postalAddress;
    }
}
