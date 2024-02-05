<?php

namespace Municipio\Admin\Acf;

/**
 * Class ContentTypeMetaFields
 * @sinve 3.61.8
 */
class ContentTypeMetaFields
{
    protected $fieldGroupKey = 'group_schema_data';

    /**
     * Class constructor.
     */
    public function __construct()
    {

        add_action('acf/init', [$this, 'registerFieldGroup']);

        add_filter('acf/load_field', [$this, 'loadField'], 1, 2);
    }

    public function loadField($field)
    {
        global $post;

        if (empty($field['contentType']) || !is_a($post, 'WP_Post')) {
            return $field;
        }

        $postContentType = \Municipio\Helper\ContentType::getContentType($post->post_type);


        if ($postContentType->getKey() === $field['contentType']) {
            return $field;
        }
        return false;
    }
    public function registerFieldGroup()
    {
        $contentTypes = \Municipio\Helper\ContentType::getRegisteredContentTypes();

        if (empty($contentTypes)) {
            return;
        }

        $locationRules = [];
        foreach ($contentTypes as $key => $value) {
            $locationRules[] =
            [
                [
                    'param'    => 'content_type',
                    'operator' => '==',
                    'value'    => $key,
                ]
            ];
        }

        acf_add_local_field_group([
            'key'      => $this->fieldGroupKey,
            'title'    => __('Schema data', 'municipio'),
            'fields'   =>  [],
            'location' => $locationRules
        ]);
        $this->registerFields($this->fieldGroupKey);
    }
    public function registerFields($fieldGroup)
    {
        $contentTypes = \Municipio\Helper\ContentType::getRegisteredContentTypes(true);
        if (empty($contentTypes) || !is_array($contentTypes)) {
            return;
        }

        foreach ($contentTypes as $contentType) {
            if (!empty($contentType['instance']->schemaParams)) {
                $schemaParams = (array) $contentType['instance']->schemaParams;
                if (empty($schemaParams)) {
                    continue;
                }
                foreach ($schemaParams as $key => $field) {
                    $this->registerField($key, $field, $fieldGroup, $contentType['instance']->getKey());
                }
            }
        }
    }
    /**
     * @param string $key The field key.
     * @param array $field The field data.
     * @param string $fieldGroup The field group key.
     * @param string $contentType The content type key.
     */
    public function registerField(string $key, array $field, string $fieldGroup, string $contentType): void
    {
        $schemaType = $field['schemaType'];

        // Check for a Google API key to determine if the 'google_map' field type can be used
        $apiKey = acf_get_setting('google_api_key');
        if (empty($apiKey)) {
            $api    = apply_filters('acf/fields/google_map/api', []);
            $apiKey = $api['key'] ?? '';
        }

        if (empty($apiKey) && $schemaType === 'GeoCoordinates') {
            // Fallback to 'group' field type with sub-fields for address components
            $field['type']       = 'group';
            $field['sub_fields'] = $this->getPostalAddressSubFields(true);
        } elseif ($schemaType === 'GeoCoordinates') {
            $field['type'] = 'google_map';
        } elseif ($schemaType === 'PostalAddress') {
            // Setup for 'PostalAddress' remains unchanged
            $field['type']       = 'group';
            $field['sub_fields'] = $this->getPostalAddressSubFields(true);
        } else {
            // Handling for other schema types remains unchanged
            $field['type'] = $this->getFieldTypeBySchema($schemaType);
        }

        $fieldSettings = [
        'key'         => 'field_' . $key,
        'label'       => $field['label'] ?? sprintf(__('Automatically registered field (%s, %s)'), $key, $schemaType),
        'type'        => $field['type'],
        'name'        => $key,
        'parent'      => $fieldGroup,
        'contentType' => $contentType,
        ];

        if (!empty($field['sub_fields'])) {
            $fieldSettings['sub_fields'] = $field['sub_fields'];
        }

        acf_add_local_field($fieldSettings);
    }
/**
 * Returns the configuration for sub-fields of a 'PostalAddress' group in ACF.
 *
 * This method defines a set of sub-fields for capturing detailed postal address
 * information. It includes fields for the street address, postal code, and country,
 * each configured with appropriate ACF field settings like key, label, name, and type.
 *
 * @return array An array of ACF field configurations for the postal address components.
 */
    protected function getPostalAddressSubFields($includeCoordinates = false): array
    {
        $fields = [
            [
                'key'          => 'field_' . uniqid('streetAddress'), // Unique key for the field
                'label'        => __('Street Address', 'municipio'), // Field label for display
                'name'         => 'streetAddress', // Field name used in the database
                'type'         => 'text', // ACF field type for a single line text input
                'instructions' => __('Enter the street address.', 'municipio'), // Optional instructions for the field
                'required'     => 0, // Whether the field is required; 1 for yes, 0 for no
            ],
            [
                'key'          => 'field_' . uniqid('postalCode'),
                'label'        => __('Postal Code', 'municipio'),
                'name'         => 'postalCode',
                'type'         => 'text',
                'instructions' => __('Enter the postal code.', 'municipio'),
                'required'     => 0,
            ],
            [
                'key'           => 'field_' . uniqid('addressCountry'),
                'label'         => __('Country', 'municipio'),
                'name'          => 'addressCountry',
                'type'          => 'text',
                'instructions'  => __('Enter the country name.', 'municipio'),
                'required'      => 0,
                'default_value' => '', // You can set a default value if needed
                'placeholder'   => __('Enter country here', 'municipio'), // Placeholder text when the field is empty
            ],
        ];
        if ($includeCoordinates) {
            array_push(
                $fields,
                [
                'key'   => 'field_latitude',
                'label' => __('Latitude', 'municipio'),
                'name'  => 'latitude',
                'type'  => 'text',
                ],
                [
                'key'   => 'field_longitude',
                'label' => __('Longitude', 'municipio'),
                'name'  => 'longitude',
                'type'  => 'text',
                ]
            );
        }
        return $fields;
    }

    /**
 * Determines the ACF field type based on the provided schema type.
 *
 * This method maps schema types to their corresponding ACF field types,
 * allowing for dynamic field type assignment based on the schema.
 * It supports a range of schema types, including 'ImageObject', 'URL',
 * and defaults to 'text' for unrecognized schema types.
 *
 * @param string $schemaType The schema type to convert to an ACF field type.
 * @return string The ACF field type corresponding to the given schema type.
 */
    protected function getFieldTypeBySchema(string $schemaType): string
    {
        switch ($schemaType) {
            case 'ImageObject':
                // Return the ACF field type for images
                return 'image';

            case 'URL':
                // Return the ACF field type for URLs
                return 'url';

            // Add more cases as needed for other schema types
            // For example:
            // case 'Date':
            //     return 'date_picker';
            // case 'DateTime':
            //     return 'date_time_picker';

            default:
                // Default to 'text' for any unrecognized schema types
                return 'text';
        }
    }
}
