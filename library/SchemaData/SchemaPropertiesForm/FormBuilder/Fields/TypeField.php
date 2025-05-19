<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

class TypeField extends AbstractField
{
    public function __construct(protected string $type)
    {
        parent::__construct('@type', '', $type);
        add_filter('acf/load_value/key=' . $this->getKey(), [$this, 'loadValue'], 10, 3);
    }

    public function toArray(): array
    {
        return [
            'type'    => 'text',
            'name'    => '@type',
            'key'     => $this->getKey(),
            'label'   => $this->getLabel(),
            'value'   => $this->getValue(),
            'wrapper' => [
                'class' => 'hidden',
            ],
        ];
    }

    public function loadValue($value, $postId, $field): string
    {
        return $this->type;
    }
}
