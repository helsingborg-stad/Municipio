<?php

namespace Municipio\Controller\ContentType;

use Municipio\Helper\ContentType as Helper;

/**
 * Class School
 * @package Municipio\Controller\ContentType
 */
class School extends ContentTypeFactory implements ContentTypeComplexInterface
{
    public function __construct()
    {
        $this->key = 'school';
        $this->label = __('School', 'municipio');

        parent::__construct($this->key, $this->label);

        $this->addSecondaryContentType(new Place());
        $this->addSecondaryContentType(new Person());

        // Append structured data for schema.org markup
        add_filter('Municipio/StructuredData', [$this, 'appendStructuredData'], 10, 3);
    }

    /**
     * addSecondaryContentType
     *
     * @param ContentTypeComponentInterface $contentType
     * @return void
     */
    public function addSecondaryContentType(ContentTypeComponentInterface $contentType): void
    {
        $this->secondaryContentType[] = $contentType;
    }
    /**
     * Appends the structured data array (used for schema.org markup) with additional data
     *
     * @param array structuredData The structured data array that we're going to append to.
     * @param string postType The post type of the current page.
     * @param int postId The ID of the post you want to add structured data to.
     *
     * @return array The modified structured data array.
     */

    public function appendStructuredData(array $structuredData, int $postId): array
    {
        if (empty($postId)) {
            return $structuredData;
        }

        $additionalData = [
            '@context' => 'https://schema.org',
            '@type' => 'School',
        ];

        $properties = Helper::getStructuredDataProperties([
            'name',
            'description', // TODO Define which meta to use for this. Use the filter hook declared in Helper for this.
            'numberOfStudents',
            'openingHours',
            'slogan' // TODO Define which meta to use for this. Use the filter hook declared in Helper for this.
        ], $postId);

        // TODO Move the foreach loop to a separate helper method
        // TODO Implement custom logic for name (post title) and slogan

        // Iterate over each property and try to fetch the corresponding meta data from the post
        foreach ($properties as $property) {
            $propertyValue =
            apply_filters(
                "Municipio/ContentType/structuredDataProperty/{$property}",
                null,
                $postId
            );

            if (is_null($propertyValue)) {
                $propertyValue = \Municipio\Helper\WP::getField($property, $postId);
            }

            $additionalData[$property] =
            apply_filters(
                "Municipio/ContentType/structuredDataProperty/{$property}/value",
                $propertyValue,
                $postId
            );
        }

        return array_merge($structuredData, $additionalData);
    }
}
