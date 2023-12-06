<?php

namespace Municipio\Controller\ContentType;

use Municipio\Controller\ContentType\School\SchoolDataPreparer;
use Municipio\Helper\ContentType as Helper;

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

    public function init(int $postId = 0): void
    {

        $this->addHooks();

        // $this->data['structuredData'] = $this->appendStructuredData($postId);
        // echo '<pre>' . print_r( $this, true ) . '</pre>';die;

    }

    public function addHooks(): void
    {
        $dataPreparer = new SchoolDataPreparer();

        // add_filter('Municipio/StructuredData', [$this, 'appendStructuredData'], 10, 1);

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

    public function getStructuredData(int $postId)
    {

        $structuredData = [
            '@type' => 'School',
        ];

        $properties = \Municipio\Helper\ContentType::getStructuredDataProperties([
            'name',
            'description', // TODO Define which meta to use for this. Use the filter hook declared in Helper for this.
            'numberOfStudents',
            'openingHours',
            'slogan' // TODO Define which meta to use for this. Use the filter hook declared in Helper for this.
        ], $postId);

        return \Municipio\Helper\ContentType::appendStructuredData($properties, $postId);
    }
}