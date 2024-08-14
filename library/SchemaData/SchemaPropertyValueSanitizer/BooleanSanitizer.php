<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

/**
 *
 * Class BooleanSanitizer
 *
 * This class implements the SchemaPropertyValueSanitizer interface and provides methods to sanitize boolean values.
 */
class BooleanSanitizer implements SchemaPropertyValueSanitizer
{
    /**
     * Class constructor.
     *
     * @param mixed $inner The inner sanitizer.
     */
    public function __construct(private $inner = new NullSanitizer())
    {
    }

    /**
     * @inheritDoc
     */
    public function sanitize(mixed $value, array $allowedTypes): mixed
    {
        if (in_array('bool', $allowedTypes) && is_array($value)) {
            return array_map(fn ($v) => $this->sanitizeBoolean($v), $value);
        }

        if (in_array('bool', $allowedTypes)) {
            return $this->sanitizeBoolean($value);
        }

        return $this->inner->sanitize($value, $allowedTypes);
    }

    /**
     * Sanitize a boolean value.
     *
     * @param mixed $value The value to sanitize.
     * @return mixed The sanitized boolean value.
     */
    private function sanitizeBoolean($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value) && strtolower($value) === 'true') {
            return true;
        }

        if (is_string($value) && strtolower($value) === 'false') {
            return false;
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        return $value;
    }
}
