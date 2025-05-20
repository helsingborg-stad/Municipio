<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

/**
 * Class GroupField
 *
 * This class is responsible for creating a group field.
 */
class GroupField extends AbstractField implements FieldWithSubFieldsInterface
{
    /**
     * GroupField constructor.
     *
     * @param string $name
     * @param string $label
     * @param array  $subFields
     */
    public function __construct(protected string $name, protected string $label, private array $subFields)
    {
        parent::__construct($name, $label, []);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type'       => 'group',
            'key'        => $this->getKey(),
            'name'       => $this->getName(),
            'label'      => $this->getLabel(),
            'sub_fields' => $this->renderSubFields(),
            'wrapper'    => [
                'class' => 'acf-admin-page',
            ],
        ];
    }

    /**
     * Get the subfields of the group field.
     *
     * @return array
     */
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
