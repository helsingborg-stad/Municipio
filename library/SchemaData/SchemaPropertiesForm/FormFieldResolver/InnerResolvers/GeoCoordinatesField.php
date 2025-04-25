<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers;

use Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\FormFieldResolverInterface;

/**
 * Class GeoCoordinatesField
 *
 * This class is responsible for resolving the form field properties for the GeoCoordinates type.
 */
class GeoCoordinatesField implements FormFieldResolverInterface
{
    /**
     * GeoCoordinatesField constructor.
     *
     * @param array $acceptedPropertyTypes The accepted property types.
     * @param FormFieldResolverInterface $inner The inner form field resolver.
     */
    public function __construct(
        private array $acceptedPropertyTypes,
        private FormFieldResolverInterface $inner
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolve(): array
    {
        $field = $this->inner->resolve();

        if (!in_array('GeoCoordinates', $this->acceptedPropertyTypes)) {
            return $field;
        }

        return array_merge($field, [
            'type'       => 'google_map',
            'required'   => 0,
            'center_lat' => '',
            'center_lng' => '',
            'zoom'       => '',
            'height'     => '',
            'value'      => $this->sanitizeValue($field['value'] ?? []),
        ]);
    }

    /**
     * Sanitizes the field value.
     *
     * @param mixed $value The field value.
     * @return array The sanitized field value.
     */
    private function sanitizeValue(mixed $value): array
    {

        if (is_string($value) && $this->isValidJson($value)) {
            $value = json_decode($value, true);
        }

        // if is serialized
        if (is_string($value) && is_serialized($value)) {
            $value = unserialize($value);
        }

        if (is_array($value)) {
            return $this->maybeConvertFieldValueFromSchemaFormatToGoogleMapsFieldFormat($value);
        }

        return [];
    }

    /**
     * Checks if a string is valid JSON.
     *
     * @param string $string The string to check.
     * @return bool True if the string is valid JSON, false otherwise.
     */
    private function isValidJson(string $string): bool
    {
        if (function_exists('json_validate')) {
            return json_validate($string);
        }

        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }

    /**
     * Converts the field value from schema format to Google Maps field format.
     *
     * @param array $value The field value in schema format.
     * @return array The field value in Google Maps field format.
     */
    private function maybeConvertFieldValueFromSchemaFormatToGoogleMapsFieldFormat(array $value): array
    {
        if (isset($value['latitude']) && isset($value['longitude'])) {
            $value['lat'] = $value['latitude'];
            $value['lng'] = $value['longitude'];
            unset($value['latitude'], $value['longitude']);
        }

        return $value;
    }
}
