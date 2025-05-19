<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\FieldValue;

use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\FieldInterface;
use WpService\Contracts\AddFilter;

/**
 * Class RegisterFieldValue
 *
 * This class is responsible for registering the field value.
 */
class RegisterFieldValue implements RegisterFieldValueInterface
{
    /**
     * Constructor.
     *
     * @param AddFilter $wpService The WordPress service instance.
     */
    public function __construct(private AddFilter $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function register(FieldInterface $field): void
    {
        $this->wpService->addFilter("acf/load_value/key={$field->getKey()}", fn () => $field->getValue(), 10);
    }
}
