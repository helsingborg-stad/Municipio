<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

class SelectField extends AbstractField implements FieldInterface
{
    public function __construct(protected string $name, protected string $label, protected mixed $value = null, protected array $choices = [])
    {
        parent::__construct($name, $label, $value);
    }

    public function toArray(): array
    {
        return [
            'type'    => 'select',
            'name'    => $this->name,
            'key'     => $this->getKey(),
            'label'   => $this->label,
            'choices' => $this->getChoices(),
        ];
    }

    public function sanitizeValue(mixed $value = null): mixed
    {
        return filter_var($value, FILTER_SANITIZE_EMAIL) ?: '';
    }

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
