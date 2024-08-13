<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

class NullSanitizer implements SchemaPropertyValueSanitizer
{
    public function sanitize(mixed $value, array $allowedTypes): mixed
    {
        return null;
    }
}
