<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\BaseType;
use Municipio\Schema\GeoCoordinates;
use Municipio\Schema\Schema;

/**
 * Class GeoCoordinatesHandler
 *
 * Handles the GeoCoordinates property for schema objects.
 */
class GeoCoordinatesHandler implements SchemaPropertyHandlerInterface
{
    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function supports(string $propertyName, string $fieldType, mixed $value, array $propertyTypes): bool
    {
        return $fieldType === 'google_map' && in_array('GeoCoordinates', $propertyTypes) && is_array($value) && $this->valueHasGoogleMapFieldContents($value);
    }

    /**
     * Check if the value has Google Map field contents.
     *
     * @param array $value The value to check.
     * @return bool True if the value has Google Map field contents, false otherwise.
     */
    private function valueHasGoogleMapFieldContents(array $value): bool
    {
        return isset($value['lat']) && isset($value['lng']) && isset($value['address']);
    }

    /**
     * @inheritDoc
     */
    public function handle(BaseType $schemaObject, string $propertyName, mixed $value): BaseType
    {
        return $schemaObject->setProperty($propertyName, $this->getGeoCoordinatesFromValue($value));
    }

    /**
     * Get the GeoCoordinates object from the value.
     *
     * @param array $value The value to convert.
     * @return GeoCoordinates The GeoCoordinates object.
     */
    private function getGeoCoordinatesFromValue(array $value): GeoCoordinates
    {
        return Schema::geoCoordinates()
            ->setProperty('latitude', $value['lat'])
            ->setProperty('longitude', $value['lng'])
            ->setProperty('address', $value['address']);
    }
}
