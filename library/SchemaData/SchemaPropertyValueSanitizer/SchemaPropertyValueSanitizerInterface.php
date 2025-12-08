<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

interface SchemaPropertyValueSanitizerInterface
{
    /**
     * Sanitize the value based on the allowed types.
     *
     * @param mixed $value The value to sanitize.
     * @param string[] $allowedTypes The allowed types for the value.
     * @return mixed The sanitized value.
     */
    public function sanitize(mixed $value, array $allowedTypes): mixed;
}
