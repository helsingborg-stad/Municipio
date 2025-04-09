<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

/**
 * StringSanitizer.
 *
 * Sanitizes string values.
 */
class StringSanitizer implements SchemaPropertyValueSanitizerInterface
{
    /**
     * Constructor.
     */
    public function __construct(private $inner = new NullSanitizer())
    {
    }

    /**
     * @inheritDoc
     */
    public function sanitize(mixed $value, array $allowedTypes): mixed
    {
        if (is_string($value) && in_array('string', $allowedTypes)) {
            return $value;
        }

        if (is_array($value) && in_array('string[]', $allowedTypes)) {
            return array_map(function ($value) {
                return $this->sanitize($value, ['string']);
            }, array_filter($value, 'is_string'));
        }

        return $this->inner->sanitize($value, $allowedTypes);
    }
}
