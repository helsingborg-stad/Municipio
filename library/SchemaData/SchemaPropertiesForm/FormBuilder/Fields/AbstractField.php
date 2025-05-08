<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

abstract class AbstractField implements FieldInterface
{
    private static int $nbrOfInstances = 0;
    protected int $instanceNumber;

    public function __construct(
        protected string $name,
        protected string $label,
        protected mixed $value = null
    ) {
        self::$nbrOfInstances++;
        $this->instanceNumber = self::$nbrOfInstances;
    }

    public function getKey(): string
    {
        return hash('crc32b', $this->getName() . $this->instanceNumber);
    }

    public function toArray(): array
    {
        return [];
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
