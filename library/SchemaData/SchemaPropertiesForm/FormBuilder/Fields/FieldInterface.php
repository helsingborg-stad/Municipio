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

    public function getValue(): mixed;

    public function setValue(mixed $value): void;

    public function getKey(): string;

    public function getName(): string;

    public function getLabel(): string;
}
