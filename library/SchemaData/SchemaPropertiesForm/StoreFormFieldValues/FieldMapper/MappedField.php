<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\FieldMapper;

/**
 * Class MappedField
 *
 * Represents a mapped field with its name, type, and value.
 */
class MappedField implements MappedFieldInterface
{
    /**
     * Constructor.
     *
     * @param string $name  The name of the field.
     * @param string $type  The type of the field.
     * @param mixed  $value The value of the field.
     */
    public function __construct(
        private string $name,
        private string $type,
        private mixed $value
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
}
