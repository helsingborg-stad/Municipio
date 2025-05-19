<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\FieldMapper;

interface MappedFieldInterface
{
    /**
     * Constructor.
     *
     * @param string $name  The name of the field.
     * @param string $type  The type of the field.
     * @param mixed  $value The value of the field.
     */
    public function getName(): string;

    /**
     * Get the type of the field.
     *
     * @return string The type of the field.
     */
    public function getType(): string;

    /**
     * Get the value of the field.
     *
     * @return mixed The value of the field.
     */
    public function getValue(): mixed;
}
