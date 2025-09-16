<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

/**
 * This class implements the SchemaPropertyValueSanitizerInterface interface and is responsible for
 * sanitizing schema property values.
 */
class SchemaPropertyValueSanitizer implements SchemaPropertyValueSanitizerInterface
{
    /**
     * @inheritDoc
     */
    public function sanitize(mixed $value, array $allowedTypes): mixed
    {
        $schemaPropSanitizer = new NullSanitizer();
        $schemaPropSanitizer = new StringSanitizer($schemaPropSanitizer);
        $schemaPropSanitizer = new BooleanSanitizer($schemaPropSanitizer);
        $schemaPropSanitizer = new NumberSanitizer($schemaPropSanitizer);
        $schemaPropSanitizer = new DateTimeSanitizer($schemaPropSanitizer);
        $schemaPropSanitizer = new PlaceFromAcfGoogleMapsFieldSanitizer($schemaPropSanitizer);
        $schemaPropSanitizer = new GeoCoordinatesFromAcfGoogleMapsFieldSanitizer($schemaPropSanitizer);
        $schemaPropSanitizer = new ArrayOfImagesSanitizer($schemaPropSanitizer);

        return $schemaPropSanitizer->sanitize($value, $allowedTypes);
    }
}
