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
class ContentTypeMetaFieldManager
{
    protected $fieldGroupKey = 'group_schema';
    protected $fieldKey      = 'field_schema';
    protected $groupName     = 'schema';

    /**
     * Class constructor.
     */
    public function __construct()
    {
        add_action('acf/init', [$this, 'registerFieldGroup']);
        add_filter('acf/prepare_field', [$this, 'maybeLoadField'], 10, 2);

        add_action('acf/save_post', [$this, 'saveAddress'], 10, 1);
    }

    /**
     * Checks if a field should be loaded based on the current post type and field properties.
     *
     * @param array $field The field to check.
     * @return mixed The field if it should be loaded, false otherwise.
     */
    public function maybeLoadField($field)
    {
        $postType = \Municipio\Helper\WP::getCurrentPostType();

        if (
            // this is the main 'schema' field group, always load it:
            $field['key'] === $this->fieldKey
            // this is the description field for the 'schema' field group, always load it:
            || !str_contains($field['key'], "{$this->groupName}_description")
            // this is a field that doesn't belong to the 'schema' field group, always load it:
            || !str_contains($field['id'], $this->groupName)
            // if for some reason no post type is returned, bail out:
            || empty($postType)
        ) {
            return $field;
        }

        $postContentType = \Municipio\Helper\ContentType::getContentType($postType);

        if (!str_contains($field['id'], $postContentType->getKey())) {
            // this is a 'schema' sub field that doesn't belong to the current content type, don't load it:
            return false;
        }

        return $field;
    }

    /**
     * Register the field group for schema data.
     * Display it on all posts of any registered content types.
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
            'title'    => __('Schema.org Data', 'municipio'),
            'location' => $locationRules,

            'fields'   => [
                [
                    'key'       => 'field_message_schema_description',
                    'label'     => '',
                    'name'      => '',
                    'type'      => 'message',
                    'message'   => __("Use these fields to enhance visibility in search results.", 'municipio'),
                    'new_lines' => '',
                ],
                [
                    'key'        => $this->fieldKey,
                    'label'      => null,
                    'name'       => $this->groupName,
                    'type'       => 'group',
                    'sub_fields' => $this->setupSubFields(),
                ]
            ]
        ]);
    }

    /**
     * Set up the sub fields for the field group.
     *
     * @return array The sub fields.
     */
    public function setupSubFields()
    {
        $fields       = [];
        $contentTypes = \Municipio\Helper\ContentType::getRegisteredContentTypes(true);
        if (empty($contentTypes) || !is_array($contentTypes)) {
            return $fields;
        }

        foreach ($contentTypes as $contentType) {
            $fields = array_merge($fields, $this->processContentType($contentType));
        }

        return $fields;
    }

    /**
     * Process a single content type and return the fields.
     *
     * @param array $contentType The content type to process.
     * @return array The fields for the content type.
     */
    protected function processContentType($contentType)
    {
        $fields = [];
        if (empty($contentType['instance']->getSchemaParams())) {
            return $fields;
        }

        foreach ($contentType['instance']->getSchemaParams() as $key => $fieldParams) {
            $fields = array_merge($fields, $this->processFieldParams($key, $fieldParams, $contentType));
        }

        return $fields;
    }

    /**
     * Process field parameters and return the field settings.
     *
     * @param string $key The field key.
     * @param array $fieldParams The field parameters.
     * @param array $contentType The content type.
     * @return array The field settings.
     */
    protected function processFieldParams($key, $fieldParams, $contentType)
    {
        $fields = [];
        switch ($fieldParams['schemaType']) {
            case 'GeoCoordinates':
                $fields = $this->handleGeoCoordinates($fieldParams, $contentType);
                break;

            default:
                $fields[] = $this->getSubFieldSettings($key, $fieldParams, $contentType['instance']->getKey());
                break;
        }

        return $fields;
    }

    /**
     * Handle GeoCoordinates schema type.
     *
     * @param array $fieldParams The field parameters.
     * @param array $contentType The content type.
     * @return array The fields for GeoCoordinates.
     */
    protected function handleGeoCoordinates($fieldParams, $contentType)
    {
        $fields                            = [];
        $postalAddressParams               = $fieldParams;
        $postalAddressParams['schemaType'] = 'PostalAddress';

        if (!empty($this->getGoogleApiKey())) {
            $fields[]                       = $this->getSubFieldSettings('geo', $fieldParams, $contentType['instance']->getKey());
            $postalAddressParams['wrapper'] = ['class' => 'hidden'];
        }

        $fields[] = $this->getSubFieldSettings('address', $postalAddressParams, $contentType['instance']->getKey());
        return $fields;
    }

    /**
     * Get the settings for a sub field.
     *
     * @param string $key The field key.
     * @param array $fieldParams The field parameters.
     * @param string $contentType The content type.
     * @return array The sub field settings.
     */
    public function getSubFieldSettings(string $key, array $fieldParams, string $contentType): array
    {
        $schemaType = $fieldParams['schemaType'];
        if ($schemaType === 'PostalAddress') {
            $fieldParams['sub_fields'] = $this->getPostalAddressSubFields();
        } elseif ($schemaType === 'ImageObject') {
            $fieldParams['return_format'] = 'id';
        }

        $fieldParams['type'] = $this->getFieldTypeBySchema($schemaType);


        $fieldSettings = [
            'key'   => 'field_' . $key . '_' . $contentType,
            'label' => $fieldParams['label'] ??
            sprintf(__('Automatically registered field (%s, %s)'), $key, $schemaType),
            'type'  => $fieldParams['type'],
            'name'  => $key,
        ];

        if (!empty($fieldParams['wrapper'])) {
            $fieldSettings['wrapper'] = $fieldParams['wrapper'];
        }

        if (!empty($fieldParams['sub_fields'])) {
            $fieldSettings['sub_fields'] = $fieldParams['sub_fields'];
        }

        return $fieldSettings;
    }

    /**
     * Get the Google API key.
     *
     * @return string|null The Google API key.
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
     * Get the sub fields for the postal address field.
     *
     * @return array The sub fields for the postal address field.
     */
    protected function getPostalAddressSubFields(): array
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
            case 'Email':
                return 'email';
            default:
                return 'text';
        }
    }

    /**
     * Update the 'address' field when a post with 'geo' is saved.
     *
     * @param int $postId The post ID.
     * @return void
     */
    public function saveAddress($postId)
    {
        $schemaData = get_field('schema', $postId);

        if (empty($schemaData)) {
            return;
        }
        if (empty($schemaData['geo'])) {
            return;
        }
        $schemaData['address'] = [
            'streetAddress'  => $schemaData['geo']['street_name'] . ' ' . $schemaData['geo']['street_number'],
            'postalCode'     => $schemaData['geo']['post_code'],
            'addressCountry' => $schemaData['geo']['country'],
        ];

        update_field('schema', $schemaData, $postId);
    }
}
