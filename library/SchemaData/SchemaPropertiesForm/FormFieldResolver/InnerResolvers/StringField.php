<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers;

use Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\FormFieldResolverInterface;

/**
 * Class StringField
 *
 * This class is responsible for creating a form field with string type.
 */
class StringField implements FormFieldResolverInterface
{
    /**
     * StringField constructor.
     *
     * @param array $acceptedPropertyTypes The accepted property types.
     * @param FormFieldResolverInterface $inner The inner form field resolver.
     */
    public function __construct(
        private array $acceptedPropertyTypes,
        private FormFieldResolverInterface $inner
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolve(): array
    {
        if (!in_array('string', $this->acceptedPropertyTypes)) {
            return $this->inner->resolve();
        }

        return array_merge($this->inner->resolve(), [
            'type'          => 'text',
            'default_value' => '',
            'required'      => 0,
        ]);
    }
}
