<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

/**
 * NumberSanitizer.
 *
 * Sanitizes number values.
 */
class NumberSanitizer implements SchemaPropertyValueSanitizerInterface
{
    /**
     * Constructor.
     */
    public function __construct(private SchemaPropertyValueSanitizerInterface $inner = new NullSanitizer())
    {
    }

    /**
     * @inheritDoc
     */
    public function sanitize(mixed $value, array $allowedTypes): mixed
    {
        if (is_float($value) && in_array('float', $allowedTypes)) {
            return (float)$value;
        }

        if (is_numeric($value) && in_array('int', $allowedTypes)) {
            return (int)$value;
        }

        if (is_array($value) && (in_array('int[]', $allowedTypes) || in_array('float[]', $allowedTypes))) {
            return array_map(function ($value) {
                return $this->sanitize($value, ['int', 'float']);
            }, array_filter($value, 'is_numeric'));
        }

        return $this->inner->sanitize($value, $allowedTypes);
    }
}
