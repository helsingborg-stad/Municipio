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
    /**
     * If a content type is set for the field,
     * only load the field if the current post types content type matches the field's content type.
     *
     * @param array $field The field to load.
     * @return array|false The loaded field or false if not applicable.
     */
    public function maybeLoadField($field)
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
            'title'    => __('Structured Data (schema.org)', 'municipio'),
            'location' => $locationRules,
            'fields'   => [[
                'key'        => $this->fieldKey,
                'label'      => null,
                'name'       => $this->groupName,
                'type'       => 'group',
                'sub_fields' => $this->setupSubFields(),
            ]]
        ]);
    }

    /**
     * Set up the sub fields for the field group.
     *
     * @return array The sub fields.
     */
    public function setupSubFields()
    {

        $contentTypes = \Municipio\Helper\ContentType::getRegisteredContentTypes(true);
        $fields       = [];

        if (empty($contentTypes) || !is_array($contentTypes)) {
            return;
        }

        foreach ($contentTypes as $contentType) {
            if (!empty($contentType['instance']->getSchemaParams())) {
                foreach ($contentType['instance']->getSchemaParams() as $key => $fieldParams) {
                    switch ($fieldParams['schemaType']) {
                        case 'GeoCoordinates':
                            // Always add the 'address' field (field type "group",
                            // containing sub fields for streetAddress, postalCode
                            // and addressCountry) to the schema field group.
                            $postalAddressParams               = $fieldParams;
                            $postalAddressParams['schemaType'] = 'PostalAddress';

                            // If there's a Google API key,
                            // add the 'geo' field (field type "google_map")
                            // to the schema field group and hide the 'address' field from the UI.
                            if (!empty($this->getGoogleApiKey())) {
                                $fields[]                       = $this->getSubFieldSettings(
                                    'geo',
                                    $fieldParams,
                                    $contentType['instance']->getKey()
                                );
                                $postalAddressParams['wrapper'] = [
                                    'class' => 'hidden',
                                ];
                            }

                            $fields[] = $this->getSubFieldSettings(
                                'address',
                                $postalAddressParams,
                                $contentType['instance']->getKey()
                            );
                            break;

                        default:
                            $fields[] = $this->getSubFieldSettings(
                                $key,
                                $fieldParams,
                                $contentType['instance']->getKey()
                            );
                            break;
                    }
                }
            }
        }

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
            $fieldParams['type']       = $this->getFieldTypeBySchema($schemaType);
            $fieldParams['sub_fields'] = $this->getPostalAddressSubFields();
        } else {
            $fieldParams['type'] = $this->getFieldTypeBySchema($schemaType);
        }

        $fieldSettings = [
            'key'         => 'field_' . $key,
            'label'       => $fieldParams['label'] ??
            sprintf(__('Automatically registered field (%s, %s)'), $key, $schemaType),
            'type'        => $fieldParams['type'],
            'name'        => $key,
            'contentType' => $contentType,
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
}
