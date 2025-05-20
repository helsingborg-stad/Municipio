<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\FieldMapper;

interface MappedFieldInterface
{
    /**
     * Constructor.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the type of the field.
     *
     * @return string The type of the field.@p
     */
    public function getType(): string;

    /**
     * Get the value of the field.
     *
     * @return mixed The value of the field.
     */
    public function getValue(): mixed;
}
