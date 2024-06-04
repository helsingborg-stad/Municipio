<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

class StringSanitizer implements SchemaPropertyValueSanitizer
{
    public function __construct(private $inner = new NullSanitizer())
    {
    }

    public function sanitize(mixed $value, array $allowedTypes): mixed
    {
        if (is_string($value) && in_array('string', $allowedTypes)) {
            return $value;
        }

        return $this->inner->sanitize($value, $allowedTypes);
    }
}
