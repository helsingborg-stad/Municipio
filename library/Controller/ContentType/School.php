<?php

namespace Municipio\Controller\ContentType;

use Municipio\Controller\ContentType\School\SchoolDataPreparer;
use Municipio\Helper\ContentType as ContentTypeHelper;

/**
 * Class School
 * @package Municipio\Controller\ContentType
 */
class School extends ContentTypeFactory implements ContentTypeComplexInterface
{

    public $secondaryContentType = [];
    protected object $postMeta;

    public function __construct()
    {
        $this->key = 'school';
        $this->label = __('School', 'municipio');
        
       
        parent::__construct($this->key, $this->label);

        $this->addSecondaryContentType(new Place());
        $this->addSecondaryContentType(new Person());

    }

    public function addHooks(): void
    {
        $dataPreparer = new SchoolDataPreparer();

        add_filter('Municipio/viewData', [$dataPreparer, 'prepareData'], 10, 1);
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

    public function getStructuredData(int $postId) : array
    {

        $structuredData = [
            '@type'            => 'School',
            'name'             => get_the_title($postId),
            'description'      => get_the_excerpt($postId),
        ];

        $meta = [
            'numberOfStudents',
            'openingHours',
            'slogan' // TODO Define which meta to use for this.
        ];

        return ContentTypeHelper::getStructuredData($postId, $structuredData, $meta);
        
    }
}