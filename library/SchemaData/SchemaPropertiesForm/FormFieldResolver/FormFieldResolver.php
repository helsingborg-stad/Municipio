<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver;

use Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers\{
    DateTimeField,
    EmptyField,
    FieldWithIdentifiers,
    GeoCoordinatesField,
    StringField
};

/**
 * Class FormFieldResolver
 *
 * This class is responsible for resolving the form field properties.
 */
class FormFieldResolver implements FormFieldResolverInterface
{
    /**
     * FormFieldResolver constructor.
     */
    public function __construct(
        private array $acceptedPropertyTypes,
        private string $propertyName
    ) {
    }

    /**
     * Resolve ACF form field properties
     *
     * @return array
     */
    public function resolve(): array
    {
        $resolver = new EmptyField();
        $resolver = new StringField($this->acceptedPropertyTypes, $resolver);
        $resolver = new DateTimeField($this->acceptedPropertyTypes, $resolver);
        $resolver = new GeoCoordinatesField($this->acceptedPropertyTypes, $resolver);
        $resolver = new FieldWithIdentifiers($this->propertyName, $resolver);

        return $resolver->resolve();
    }
}
