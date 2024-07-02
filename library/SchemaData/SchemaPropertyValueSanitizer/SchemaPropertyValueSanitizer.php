<?php

namespace Municipio\SchemaData\SchemaPropertyValueSanitizer;

interface SchemaPropertyValueSanitizer
{
    public function sanitize(mixed $value, array $allowedTypes): mixed;
}
