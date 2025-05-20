<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

/**
 * Class AbstractField
 *
 * This class is the base class for all field types.
 */
abstract class AbstractField implements FieldInterface
{
    private static int $nbrOfInstances = 0;
    protected int $instanceNumber;

    /**
     * AbstractField constructor.
     *
     * @param string $name
     * @param string $label
     * @param mixed  $value
     */
    public function __construct(
        protected string $name,
        protected string $label,
        protected mixed $value = null
    ) {
        self::$nbrOfInstances++;
        $this->instanceNumber = self::$nbrOfInstances;
    }

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return hash('crc32b', $this->getName() . $this->instanceNumber);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getValue(): mixed
    {
        return $this->value;
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
    public function getLabel(): string
    {
        return $this->label;
    }
}
