<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

interface FieldInterface
{
    /**
     * Get the field settings as an array.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Get the field value.
     *
     * @return mixed
     */
    public function getValue(): mixed;

    /**
     * Get the field key.
     *
     * @return string
     */
    public function getKey(): string;

    /**
     * Get the field name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the field label.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Get the field instructions.
     *
     * @return string|null
     */
    public function getInstructions(): null|string;
}
