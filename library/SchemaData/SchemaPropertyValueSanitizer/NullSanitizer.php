<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

/**
 * NullSanitizer.
 */
class NullSanitizer implements SchemaPropertyValueSanitizerInterface
{
    /**
     * @inheritDoc
     *
     * @return mixed
     */
    public function sanitize(mixed $value, array $allowedTypes): mixed
    {
        return $value;
    }
}
