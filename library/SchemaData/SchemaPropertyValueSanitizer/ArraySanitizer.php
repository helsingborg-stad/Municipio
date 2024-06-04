<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

class ArraySanitizer implements SchemaPropertyValueSanitizer
{
    public function __construct(private $inner = new NullSanitizer())
    {
    }

    public function sanitize(mixed $value, array $allowedTypes): mixed
    {
        $value = $this->inner->sanitize($value, $allowedTypes);

        if (!empty($value)) {
            return $value;
        }

        // If anything in the array ends with "[]"
        if (preg_grep('/\[\]$/', $allowedTypes) && is_array($value)) {
            $allowedNonArrayableTypes = array_map(function ($type) {
                return preg_replace('/\[\]$/', '', $type);
            }, $allowedTypes);

            return array_map(function ($item) use ($allowedNonArrayableTypes) {
                return $this->inner->sanitize($item, $allowedNonArrayableTypes);
            }, $value);
        }

        return $value;
    }
}
