<?php 

namespace Municipio\Admin\Acf\ContentTypeSchema;

use Municipio\Admin\Acf\ContentTypeMetaFieldManager as FieldManager;

class RegisterFields {
    private $fieldManager;

    public function __construct(FieldManager $fieldManager) {
        $this->fieldManager = $fieldManager;
    }
    /**
     * Registers the field group for schema data to be displayed on all posts of any registered content types.
     */
    public function setup()
    {
        if( !function_exists('acf_add_local_field_group') ) {
            return;
        }

        $contentTypes = \Municipio\Helper\ContentType::getRegisteredContentTypes();

        if (empty($contentTypes)) {
            return;
        }

        $locationRules = array_map(function ($key) {
            return [['param' => 'content_type', 'operator' => '==', 'value' => $key]];
        }, array_keys($contentTypes));

        acf_add_local_field_group([
            'key'      => $this->fieldManager->getFieldGroupKey(),
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
                    'key'        => $this->fieldManager->getFieldKey(),
                    'label'      => null,
                    'name'       => $this->fieldManager->getGroupName(),
                    'type'       => 'group',
                    'sub_fields' => $this->setupSubFields(),
                ]
            ]
        ]);
    }

    /**
     * Sets up the sub fields for the field group based on registered content types.
     *
     * @return array The configured sub fields.
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
     * Processes a single content type to generate its schema fields.
     *
     * @param array $contentType The content type to process.
     * @return array The generated fields for the content type.
     */
    protected function processContentType($contentType)
    {
        $fields = [];
        if (empty($contentType['instance']->getSchemaParams())) {
            return $fields;
        }

        foreach ($contentType['instance']->getSchemaParams() as $key => $fieldParams) {
            if ($fieldParams['schemaType'] === 'GeoCoordinates') {
                $fields = array_merge($fields, $this->handleGeoCoordinates($fieldParams, $contentType));
            } else {
                $fields[] = $this->getSubFieldSettings($key, $fieldParams, $contentType['instance']->getKey());
            }
        }

        return $fields;
    }

    /**
     * Handles the special case of GeoCoordinates schema type.
     *
     * @param array $fieldParams The parameters for the GeoCoordinates field.
     * @param array $contentType The content type being processed.
     * @return array The fields configured for GeoCoordinates.
     */
    protected function handleGeoCoordinates($fieldParams, $contentType)
    {
        $fields                            = [];
        $postalAddressParams               = $fieldParams;
        $postalAddressParams['schemaType'] = 'PostalAddress';

        if (!empty($this->getGoogleApiKey())) {
            $fields[]                       = $this->getSubFieldSettings(
                'geo',
                $fieldParams,
                $contentType['instance']->getKey()
            );
            $postalAddressParams['wrapper'] = ['class' => 'hidden'];
        }

        $fields[] = $this->getSubFieldSettings('address', $postalAddressParams, $contentType['instance']->getKey());
        return $fields;
    }

    /**
     * Gets the settings for a sub field based on its key and parameters.
     *
     * @param string $key The field key.
     * @param array $fieldParams The field parameters.
     * @param string $contentTypeKey The key for the content type.
     * @return array The configured sub field settings.
     */
    public function getSubFieldSettings(string $key, array $fieldParams, string $contentTypeKey): array
    {
        if ($fieldParams['schemaType'] === 'PostalAddress') {
            $fieldParams['sub_fields'] = $this->getPostalAddressSubFields();
        } elseif ($fieldParams['schemaType'] === 'ImageObject') {
            $fieldParams['return_format'] = 'id';
        }

        $fieldParams['type'] = $this->getFieldTypeBySchema($fieldParams['schemaType']);

        return [
            'key'        => 'field_' . $key . '_' . $contentTypeKey,
            'label'      =>
            $fieldParams['label']
            ?? sprintf(__('Automatically registered field (%s, %s)'), $key, $fieldParams['schemaType']),
            'type'       => $fieldParams['type'],
            'name'       => $key,
            'wrapper'    => $fieldParams['wrapper'] ?? [],
            'sub_fields' => $fieldParams['sub_fields'] ?? [],
        ];
    }
    /**
     * Defines the sub fields for the postal address schema type.
     *
     * @return array The sub fields for the postal address.
     */
    protected function getPostalAddressSubFields(): array
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
    /**
     * Maps a schema type to an ACF field type.
     *
     * @param string $schemaType The schema type.
     * @return string The corresponding ACF field type.
     */
    protected function getFieldTypeBySchema(string $schemaType): string
    {
        $fieldTypeMap = [
            'GeoCoordinates' => 'google_map',
            'PostalAddress'  => 'group',
            'ImageObject'    => 'image',
            'URL'            => 'url',
            'Email'          => 'email',
            'Date'           => 'date_time_picker',
        ];

        return $fieldTypeMap[$schemaType] ?? 'text';
    }
    /**
     * Retrieves the Google API key from ACF settings or filters.
     *
     * @return string|null The Google API key, if available.
     */
    protected function getGoogleApiKey()
    {
        $apiKey = acf_get_setting('google_api_key');
        return $apiKey ?: apply_filters('acf/fields/google_map/api', [])['key'] ?? null;
    }
}
