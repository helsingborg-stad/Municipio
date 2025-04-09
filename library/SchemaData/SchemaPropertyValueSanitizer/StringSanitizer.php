<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

class StringSanitizer implements SchemaPropertyValueSanitizerInterface
{
    public function __construct(private $inner = new NullSanitizer())
    {
    }

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
