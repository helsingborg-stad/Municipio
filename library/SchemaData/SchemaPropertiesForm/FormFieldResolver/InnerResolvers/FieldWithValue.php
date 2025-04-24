<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers;

use AcfService\Contracts\GetField;
use Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\FormFieldResolverInterface;

/**
 * Class FieldWithValue
 *
 * This class is responsible for resolving the form field properties for a given field with a value.
 */
class FieldWithValue implements FormFieldResolverInterface
{
    /**
     * FieldWithIdentifiers constructor.
     *
     * @param string $propertyName The name of the property.
     * @param FormFieldResolverInterface $inner The inner form field resolver.
     */
    public function __construct(
        private GetField $acfService,
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
            'value' => $this->acfService->getField(FieldWithIdentifiers::FIELD_PREFIX . $this->propertyName),
        ]);
    }
}
