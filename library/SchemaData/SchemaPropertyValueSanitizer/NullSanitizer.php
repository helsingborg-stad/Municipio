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
     * @return null
     */
    public function sanitize(mixed $value, array $allowedTypes): mixed
    {
        return null;
    }
}
