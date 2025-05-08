<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

class GroupField extends AbstractField implements FieldWithSubFieldsInterface
{
    public function __construct(protected string $name, protected string $label, private array $subFields)
    {
        parent::__construct($name, $label, []);
    }

    public function toArray(): array
    {
        return [
            'type'       => 'group',
            'key'        => $this->getKey(),
            'name'       => $this->name,
            'label'      => $this->label,
            'sub_fields' => $this->renderSubFields(),
            'wrapper'    => [
                'class' => 'acf-admin-page',
            ],
        ];
    }

    public function getSubFields(): array
    {
        return $this->subFields;
    }

    /**
     * Render the subfields as an array.
     *
     * @return array
     */
    private function renderSubFields(): array
    {
        return array_map(fn (FieldInterface $subField) => $subField->toArray(), $this->subFields);
    }
}
