<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\Sanitize;

use Municipio\Schema\Schema;

/**
 * Class SanitizeGeoCoordinates
 *
 * This class is responsible for sanitizing GeoCoordinates data.
 */
class SanitizeGeoCoordinates implements SanitizeInterface
{
    /**
     * @inheritDoc
     */
    public function sanitize(array $allowedTypes, mixed $value): mixed
    {
        if (!in_array('GeoCoordinates', $allowedTypes)) {
            return $value;
        }

        return $this->sanitizeGeoCoordinates($value);
    }

    /**
     * Sanitize GeoCoordinates data.
     *
     * @param mixed $value The value to sanitize.
     *
     * @return mixed The sanitized value.
     */
    private function sanitizeGeoCoordinates(mixed $value): mixed
    {
        // possibly turn serialized string to array
        if (is_string($value) && is_serialized($value)) {
            $value = unserialize($value);
        }

        // possibly turn escaped json string to array
        if (is_string($value) && json_validate(stripslashes($value))) {
            $value = json_decode(stripslashes($value), true);
        }

        // possibly turn json string to array
        if (is_string($value) && json_validate($value)) {
            $value = json_decode($value, true);
        }

        // possibly convert from acf google maps field value to scheme GeoCoordinates containing address, latitude and longitude
        if (is_array($value) && key_exists('address', $value) && key_exists('lat', $value) && key_exists('lng', $value)) {
            return Schema::geoCoordinates()
                ->address($value['address'])
                ->latitude($value['lat'])
                ->longitude($value['lng'])
                ->toArray();
        }

        return $value;
    }
}
