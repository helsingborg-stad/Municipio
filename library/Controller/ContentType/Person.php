<?php

namespace Municipio\Controller\ContentType;

/**
 * Class Person
 *
 * @package Municipio\Controller\ContentType
 */
class Person extends ContentTypeFactory
{

    protected $secondaryContentType = [];
    
    public function __construct()
    {
        $this->key = 'person';
        $this->label = __('Person', 'municipio');

        parent::__construct($this->key, $this->label);

    }

    public function addHooks(): void {
        // Append structured data for schema.org markup
        add_filter('Municipio/StructuredData', [$this, 'appendStructuredData'], 10, 3);

    }
    /**
     * Appends structured data for a Person to the given array.
     *
     * @param array $structuredData The array to append the structured data to.
     * @param Person $person The Person object to generate the structured data for.
     *
     * @return array The updated array with the Person structured data appended.
     */
    public function appendStructuredData(array $structuredData, string $postType, int $postId): array
    {
        if (empty($postId)) {
            return $structuredData;
        }

        $properties = \Municipio\Helper\ContentType::filteredStructuredDataProperties(
            [
                'name',
                'jobTitle',
                'email',
                'telephone'
            ], 
            $postId);

        return \Municipio\Helper\ContentType::prepareStructuredData($structuredData, 'Person', $properties, $postId);
    }
}
