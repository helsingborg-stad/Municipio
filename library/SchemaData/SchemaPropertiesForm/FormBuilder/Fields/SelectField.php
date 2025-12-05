<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

/**
 * Class SelectField
 *
 * This class is responsible for creating a select field.
 */
class SelectField extends AbstractField implements FieldInterface
{
    /**
     * SelectField constructor.
     *
     * @param string $name
     * @param string $label
     * @param mixed  $value
     * @param array  $choices
     */
    public function __construct(protected string $name, protected string $label, protected mixed $value = null, protected array $choices = [])
    {
        parent::__construct($name, $label, $value);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type'    => 'select',
            'name'    => $this->getName(),
            'key'     => $this->getKey(),
            'label'   => $this->getLabel(),
            'choices' => $this->getChoices(),
        ];
    }

    /**
     * Get the value of the field.
     *
     * @return array Options for the select field.
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
