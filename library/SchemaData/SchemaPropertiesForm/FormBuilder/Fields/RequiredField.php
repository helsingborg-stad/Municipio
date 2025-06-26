<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

/**
 * Class RequiredField
 *
 * This class is responsible for creating a required field.
 */
class RequiredField implements FieldInterface
{
    /**
     * RequiredField constructor.
     *
     * @param FieldInterface $field The field to be wrapped as required.
     */
    public function __construct(private FieldInterface $field)
    {
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [...$this->field->toArray(), 'required' => true];
    }

    /**
     * @inheritDoc
     */
    public function getValue(): mixed
    {
        return $this->field->getValue();
    }

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return $this->field->getKey();
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->field->getName();
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return $this->field->getLabel();
    }
}
