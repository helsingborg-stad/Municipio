<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

/**
 * Class MultiSelectField
 *
 * This class is responsible for creating a multi-select field.
 */
class MultiSelectField extends AbstractField implements FieldInterface
{
    /**
     * MultiSelectField constructor.
     *
     * @param string $name   The name of the field.
     * @param string $label  The label of the field.
     * @param mixed  $value  The value of the field.
     * @param array  $choices The choices for the multi-select field.
     */
    public function __construct(
        protected string $name,
        protected string $label,
        protected mixed $value = null,
        protected array $choices = [],
        protected null|string $instructions = null,
    ) {
        parent::__construct($name, $label, $value, $instructions);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type' => 'select',
            'name' => $this->getName(),
            'key' => $this->getKey(),
            'label' => $this->getLabel(),
            'instructions' => $this->getInstructions(),
            'multiple' => 1,
            'ui' => 1,
            'choices' => $this->getChoices(),
        ];
    }

    /**
     * Get the options for the multi-select field.
     *
     * @return array Options for the multi-select field.
     */
    private function getChoices(): array
    {
        $choices = [];

        foreach ($this->choices as $key => $value) {
            if (is_array($value)) {
                $choices[$key] = $value['label'];
            } else {
                $choices[$key] = $value;
            }
        }

        return $choices;
    }
}
