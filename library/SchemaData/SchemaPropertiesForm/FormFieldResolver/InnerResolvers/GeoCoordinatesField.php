<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\InnerResolvers;

use Municipio\SchemaData\SchemaPropertiesForm\FormFieldResolver\FormFieldResolverInterface;

/**
 * Class GeoCoordinatesField
 *
 * This class is responsible for resolving the form field properties for the GeoCoordinates type.
 */
class GeoCoordinatesField implements FormFieldResolverInterface
{
    /**
     * GeoCoordinatesField constructor.
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
        $field = $this->inner->resolve();

        if (!in_array('GeoCoordinates', $this->acceptedPropertyTypes)) {
            return $field;
        }

        return array_merge($field, [
            'type'       => 'google_map',
            'required'   => 0,
            'center_lat' => '',
            'center_lng' => '',
            'zoom'       => '',
            'height'     => '',
        ]);
    }
}
