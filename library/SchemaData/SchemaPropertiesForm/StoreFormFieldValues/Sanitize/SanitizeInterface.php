<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\Sanitize;

interface SanitizeInterface
{
    /**
     * Sanitize value before storing.
     *
     * @param array $allowedTypes The allowed types for the value.
     * @param mixed $value The value to sanitize.
     */
    public function sanitize(array $allowedTypes, mixed $value): mixed;
}
