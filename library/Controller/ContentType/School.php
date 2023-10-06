<?php

namespace Municipio\Controller\ContentType;

/**
 * Class School
 * @package Municipio\Controller\ContentType
 */
class School extends ContentTypeFactory implements ContentTypeComplexInterface
{

    public $secondaryContentType = [];
    
    public function __construct()
    {
        $this->key = 'school';
        $this->label = __('School', 'municipio');

        parent::__construct($this->key, $this->label);

        $this->addSecondaryContentType(new Place());
        $this->addSecondaryContentType(new Person());

    }

    public function addHooks() {
        // Append structured data for schema.org markup
        add_filter('Municipio/StructuredData', [$this, 'appendStructuredData'], 10, 4);
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

     
    public function appendStructuredData(array $structuredData, string $postType, int $postId): array
    {

        if (empty($postId)) {
            return $structuredData;
        }

        $properties = \Municipio\Helper\ContentType::filteredStructuredDataProperties(
            [
                'name',
                'description', // TODO Define which meta to use for this. Use the filter hook declared in Helper for this.
                'numberOfStudents',
                'openingHours',
                'slogan' // TODO Define which meta to use for this. Use the filter hook declared in Helper for this.
            ], 
            $postId);

        return \Municipio\Helper\ContentType::prepareStructuredData($structuredData, 'School', $properties, $postId );
    }
}
