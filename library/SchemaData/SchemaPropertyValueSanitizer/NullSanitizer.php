<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

class NullSanitizer implements SchemaPropertyValueSanitizerInterface
{
    public function sanitize(mixed $value, array $allowedTypes): mixed
    {
        return null;
    }
}
