<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\BaseType;
use Municipio\Schema\Place;
use Municipio\Schema\Schema;

class PlaceHandler implements SchemaPropertyHandlerInterface
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
        return $fieldType === 'google_map' && in_array('Place', $propertyTypes) && is_array($value) && $this->valueHasGoogleMapFieldContents($value);
    }

    private function valueHasGoogleMapFieldContents(array $value): bool
    {
        return isset($value['lat']) && isset($value['lng']) && isset($value['address']);
    }

    /**
     * @inheritDoc
     */
    public function handle(BaseType $schemaObject, string $propertyName, mixed $value): BaseType
    {
        return $schemaObject->setProperty($propertyName, $this->getPlaceFromValue($value));
    }

    private function getPlaceFromValue(array $value): Place
    {
        return Schema::place()
            ->setProperty('latitude', $value['lat'])
            ->setProperty('longitude', $value['lng'])
            ->setProperty('address', $value['address']);
    }
}
