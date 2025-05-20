<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers;

use Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\FormFieldResolverInterface;

/**
 * Class DateTimeField
 */
class DateTimeField implements FormFieldResolverInterface
{
    /**
     * DateTimeField constructor.
     *
     * @param array $acceptedPropertyTypes
     * @param FormFieldResolverInterface $inner
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
        $field = $this->inner->resolve();

        if (!in_array('\DateTimeInterface', $this->acceptedPropertyTypes)) {
            return $field;
        }

        return array_merge($field, [
            'type'           => 'date_time_picker',
            'return_format'  => 'Y-m-d H:i:s',
            'first_day'      => 1,
            'display_format' => 'Y-m-d H:i:s',
            'default_value'  => null,
            'required'       => 0,
        ]);
    }
}
