<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers;

use Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\FormFieldResolverInterface;

/**
 * Class FieldWithIdentifiers
 *
 * This class is responsible for creating a form field with identifiers.
 */
class FieldWithIdentifiers implements FormFieldResolverInterface
{
    public const FIELD_PREFIX = 'schema_';

    /**
     * FieldWithIdentifiers constructor.
     *
     * @param string $propertyName The name of the property.
     * @param FormFieldResolverInterface $inner The inner form field resolver.
     */
    public function __construct(
        private string $propertyName,
        private FormFieldResolverInterface $inner
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolve(): array
    {
        return array_merge($this->inner->resolve(), [
            'key'   => self::FIELD_PREFIX . $this->propertyName,
            'label' => $this->propertyName,
            'name'  => self::FIELD_PREFIX . $this->propertyName,
        ]);
    }
}
