<?php

namespace Municipio\Admin\Acf\ContentType\Schema\Subfields;

class PostalAddress implements SchemaBasedAcfSubfieldsInterface {
    public function getSubFields(): array 
    {
        return [
            [
                'key'          => 'field_streetAddress',
                'label'        => __('Street Address', 'municipio'),
                'name'         => 'streetAddress',
                'type'         => 'text',
                'instructions' => __('Enter the street address.', 'municipio'),
                'required'     => 0,
            ],
            [
                'key'          => 'field_postalCode',
                'label'        => __('Postal Code', 'municipio'),
                'name'         => 'postalCode',
                'type'         => 'text',
                'instructions' => __('Enter the postal code.', 'municipio'),
                'required'     => 0,
            ],
            [
                'key'           => 'field_addressCountry',
                'label'         => __('Country', 'municipio'),
                'name'          => 'addressCountry',
                'type'          => 'text',
                'instructions'  => __('Enter the country name.', 'municipio'),
                'required'      => 0,
                'default_value' => '',
                'placeholder'   => __('Enter country here', 'municipio'),
            ],
        ];
    }
}