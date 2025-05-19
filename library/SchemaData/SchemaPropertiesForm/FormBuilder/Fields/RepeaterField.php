<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

class RepeaterField extends AbstractField implements FieldWithSubFieldsInterface
{
    public function __construct(protected string $name, protected string $label, protected mixed $value = null, private array $subFields = [])
    {
        parent::__construct($name, $label, $value);

        add_filter("acf/load_value/key={$this->getKey()}", function ($value, $postId, $field) {

            foreach (array_values($value) as $index => $row) {
                $subFieldNamePrefix = $field['name'] . '_' . $index . '_';

                foreach ($this->subFields as $subField) {
                    $subFieldName  = $subFieldNamePrefix . $subField->getName();
                    $subfieldValue = $row[$subField->getKey()] ?? null;
                    add_filter("acf/load_value/name={$subFieldName}", function ($value) use ($subfieldValue) {
                        return $subfieldValue;
                    });
                }
            }

            return $value;
        }, 10, 3);
    }

    public function toArray(): array
    {
        return [
            'type'       => 'repeater',
            'name'       => $this->name,
            'key'        => $this->getKey(),
            'label'      => $this->label,
            'layout'     => 'block', // 'table' can not be used, due to incompatibility with hidden sub fields.
            'sub_fields' => $this->renderSubFields(),
        ];
    }

    private function renderSubFields(): array
    {
        return array_map(fn (FieldInterface $subField) => $subField->toArray(), $this->subFields);
    }

    public function sanitizeValue(mixed $value = null): mixed
    {
        return is_string($value) ? $value : '';
    }

    public function getSubFields(): array
    {
        return $this->subFields;
    }
}
