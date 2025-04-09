<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

interface SchemaPropertyValueSanitizerInterface
{
    public function sanitize(mixed $value, array $allowedTypes): mixed;
}
