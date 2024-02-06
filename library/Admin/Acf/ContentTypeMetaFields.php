<?php

namespace Municipio\Admin\Acf;

/**
 * Manages custom ACF field groups and fields for content types.
 * This class is responsible for registering field groups and fields based on content types,
 * ensuring fields are loaded correctly for each content type, and providing a mechanism to
 * handle field registration dynamically.
 *
 * @since 3.61.8
 */
class ContentTypeMetaFields
{
    /**
     * Key used for the ACF field group associated with schema data.
     *
     * @var string
     */
    protected $fieldGroupKey = 'group_schema_data';

    /**
     * Constructor hooks into ACF to register field groups and fields.
     */
    public function __construct()
    {
        add_action('acf/init', [$this, 'registerFieldGroup']);
        add_filter('acf/prepare_field', [$this, 'loadField'], 10, 2);
    }

    /**
     * Filters fields based on the content type of the current post.
     *
     * @param array $field The field configuration array.
     * @return array|false The modified field array or false if the field does not match the content type.
     */
    public function loadField($field)
    {

        $postType = \Municipio\Helper\WP::getCurrentPostType();

        if (empty($field['contentType']) || empty($postType)) {
            return $field;
        }

        $postContentType = \Municipio\Helper\ContentType::getContentType($postType);

        if ($postContentType->getKey() === $field['contentType']) {
            return $field;
        }

        return false;
    }

    /**
     * Registers the field group for content types, adding location rules based on registered content types.
     */
    public function registerFieldGroup()
    {
        $contentTypes = \Municipio\Helper\ContentType::getRegisteredContentTypes();

        if (empty($contentTypes)) {
            return;
        }

        $locationRules = array_map(function ($key) {
            return [
                [
                    'param'    => 'content_type',
                    'operator' => '==',
                    'value'    => $key,
                ],
            ];
        }, array_keys($contentTypes));

        acf_add_local_field_group([
            'key'      => $this->fieldGroupKey,
            'title'    => __('Schema data', 'municipio'),
            'fields'   => [],
            'location' => $locationRules,
        ]);

        $this->registerFields($this->fieldGroupKey);
    }

    /**
     * Iterates over registered content types and registers fields based on schema parameters.
     *
     * @param string $fieldGroup The field group key under which the fields should be registered.
     */
    public function registerFields($fieldGroup)
    {
        $contentTypes = \Municipio\Helper\ContentType::getRegisteredContentTypes(true);

        if (empty($contentTypes) || !is_array($contentTypes)) {
            return;
        }

        foreach ($contentTypes as $contentType) {
            if (!empty($contentType['instance']->getSchemaParams())) {
                foreach ($contentType['instance']->getSchemaParams() as $key => $field) {
                    $this->registerField($key, $field, $fieldGroup, $contentType['instance']->getKey());
                }
            }
        }
    }

    /**
     * Registers a single field within an ACF field group, applying a fallback mechanism for 'GeoCoordinates'.
     *
     * @param string $key The field key.
     * @param array $field The field data.
     * @param string $fieldGroup The field group key.
     * @param string $contentType The content type key.
     */
    public function registerField(string $key, array $field, string $fieldGroup, string $contentType): void
    {
        $schemaType = $field['schemaType'];

        // Check for a Google API key to determine if the 'google_map' field type can be used
        $apiKey = $this->getGoogleApiKey();

        if (empty($apiKey) && $schemaType === 'GeoCoordinates') {
            $field['type']       = 'group';
            $field['sub_fields'] = $this->getPostalAddressSubFields(true);
        } elseif ($schemaType === 'PostalAddress') {
            $field['type']       = $this->getFieldTypeBySchema($schemaType);
            $field['sub_fields'] = $this->getPostalAddressSubFields(false);
        } else {
            $field['type'] = $this->getFieldTypeBySchema($schemaType);
        }

        $fieldSettings = [
            'key'         => 'field_' . $key,
            'label'       => $field['label'] ??
            sprintf(__('Automatically registered field (%s, %s)'), $key, $schemaType),
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
     * Checks both the new and old methods for setting the Google Maps API key.
     *
     * @return string|null The Google Maps API key if set, otherwise null.
     */
    protected function getGoogleApiKey()
    {
        $apiKey = acf_get_setting('google_api_key');
        if (empty($apiKey)) {
            $api    = apply_filters('acf/fields/google_map/api', []);
            $apiKey = $api['key'] ?? null;
        }

        return $apiKey;
    }
    /**
     * Returns the configuration for sub-fields of a 'PostalAddress' group in ACF, optionally including geo-coordinates.
     *
     * @param bool $includeCoordinates Whether to include latitude and longitude fields.
     * @return array An array of ACF field configurations for postal address components, and optionally geo-coordinates.
     */
    protected function getPostalAddressSubFields($includeCoordinates = false): array
    {
        $fields = [
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

        if ($includeCoordinates) {
            $fields[] = [
            'key'   => 'field_latitude',
            'label' => __('Latitude', 'municipio'),
            'name'  => 'latitude',
            'type'  => 'text',
            ];
            $fields[] = [
            'key'   => 'field_longitude',
            'label' => __('Longitude', 'municipio'),
            'name'  => 'longitude',
            'type'  => 'text',
            ];
        }

        return $fields;
    }
    /**
     * Determines the ACF field type based on the provided schema type.
     *
     * @param string $schemaType The schema type to convert to an ACF field type.
     * @return string The ACF field type corresponding to the given schema type.
     */
    protected function getFieldTypeBySchema(string $schemaType): string
    {
        switch ($schemaType) {
            case 'GeoCoordinates':
                return 'google_map';
            case 'PostalAddress':
                return 'group';
            case 'ImageObject':
                return 'image';
            case 'URL':
                return 'url';
            default:
                return 'text';
        }
    }
}
