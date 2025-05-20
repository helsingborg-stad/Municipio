<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields;

use Municipio\Helper\WpService;

/**
 * Class TypeField
 *
 * This class is responsible for creating a hidden field that stores the type of schema.
 */
class TypeField extends AbstractField
{
    /**
     * Constructor.
     *
     * @param string $type The type of schema.
     */
    public function __construct(protected string $type)
    {
        parent::__construct('@type', '', $type);
        WpService::get()->addFilter('acf/load_value/key=' . $this->getKey(), [$this, 'loadValue'], 10);
    }

    /**
     * @inheritDoc
     */
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

    /**
     * Load the value of the field.
     *
     * @return string The type of schema.
     */
    public function loadValue(): string
    {
        return $this->type;
    }
}
