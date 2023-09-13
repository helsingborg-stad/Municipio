<?php

namespace Municipio\Controller\ContentType;

/**
 * Class Person
 *
 * @package Municipio\Controller\ContentType
 */
class Person extends ContentTypeFactory
{
    public function __construct()
    {
        $this->key = 'person';
        $this->label = __('Person', 'municipio');

        parent::__construct($this->key, $this->label);

        // Append structured data for schema.org markup
        add_filter('Municipio/StructuredData', [$this, 'appendStructuredData'], 10, 2);
    }

    /**
     * Appends structured data for a Person to the given array.
     *
     * @param array $structuredData The array to append the structured data to.
     * @param Person $person The Person object to generate the structured data for.
     *
     * @return array The updated array with the Person structured data appended.
     */
    public function appendStructuredData(array $structuredData, int $postId): array
    {
        if (empty($postId)) {
            return $structuredData;
        }

        $additionalData = [
            '@context' => 'http://schema.org',
            '@type' => 'Person',
        ];


        // TODO - Update the ACF field names in the API to match the schema.org property names (in the api-school-manager plugin)
        // Define the available properties for the Person schema
        $properties = apply_filters(
            'Municipio/ContentType/structuredDataProperties',
            [
                'name',
                'jobTitle',
                'email',
                'telephone'
            ],
            $postId
        );

        // Iterate over each property and try to fetch the corresponding meta data from the post
        foreach ($properties as $property) {
            $metaValue = get_post_meta($postId, $property, true);

            // If a value is returned, add it to the Person schema
            if (!empty($metaValue)) {
                $additionalData[$property] = $metaValue;
            }
        }

        return array_merge($structuredData, $additionalData);
    }
}
