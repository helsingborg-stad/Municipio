<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

use Municipio\Helper\WpService;

/**
 * Class RepeaterField
 *
 * This class is responsible for creating a repeater field.
 */
class RepeaterField extends AbstractField implements FieldWithSubFieldsInterface
{
    /**
     * RepeaterField constructor.
     *
     * @param string $name
     * @param string $label
     * @param mixed  $value
     * @param array  $subFields
     */
    public function __construct(protected string $name, protected string $label, protected mixed $value = null, private array $subFields = [])
    {
        parent::__construct($name, $label, $value);

        WpService::get()->addFilter("acf/load_value/key={$this->getKey()}", function ($value, $postId, $field) {

            foreach (array_values($value) as $index => $row) {
                $subFieldNamePrefix = $field['name'] . '_' . $index . '_';

                foreach ($this->subFields as $subField) {
                    $subFieldName  = $subFieldNamePrefix . $subField->getName();
                    $subfieldValue = $row[$subField->getKey()] ?? null;
                    WpService::get()->addFilter("acf/load_value/name={$subFieldName}", function ($value) use ($subfieldValue) {
                        return $subfieldValue;
                    });
                }
            }

            return $value;
        }, 10, 3);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type'       => 'repeater',
            'name'       => $this->getName(),
            'key'        => $this->getKey(),
            'label'      => $this->getLabel(),
            'layout'     => 'block', // 'table' can not be used, due to incompatibility with hidden sub fields.
            'sub_fields' => $this->renderSubFields(),
        ];
    }

    /**
     * Render the sub fields of the repeater.
     *
     * @return array
     */
    private function renderSubFields(): array
    {
        return array_map(fn (FieldInterface $subField) => $subField->toArray(), $this->subFields);
    }

    /**
     * @inheritDoc
     */
    public function getSubFields(): array
    {
        return $this->subFields;
    }
}
