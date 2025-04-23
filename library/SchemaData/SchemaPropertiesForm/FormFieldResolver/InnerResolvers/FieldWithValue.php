<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers;

use Municipio\Helper\AcfService;
use Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\FormFieldResolverInterface;

use function AcfService\Implementations\get_field;

class FieldWithValue implements FormFieldResolverInterface
{
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
            'value' => AcfService::get()->getField(FieldWithIdentifiers::FIELD_PREFIX . $this->propertyName),
        ]);
    }
}
