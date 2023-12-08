<?php

namespace Municipio\Controller\ContentType;

use Municipio\Helper\ContentType as ContentTypeHelper;

/**
 * Class Person
 *
 * @package Municipio\Controller\ContentType
 */
class Person extends ContentTypeFactory
{

    public $secondaryContentType = [];
    
    public function __construct()
    {
        $this->key = 'person';
        $this->label = __('Person', 'municipio');

        parent::__construct($this->key, $this->label);
    }

    public function init(): void
    {
        $this->addHooks();
    }

    public function addHooks(): void {
       
    }
    public function getStructuredData(int $postId) : array
    {

        $structuredData = [
            '@type'            => 'Person',
            'name'             => get_the_title($postId),
        ];

        $meta = [
            'jobTitle',
            'email',
            'telephone'
        ];

        return ContentTypeHelper::getStructuredData($postId, $structuredData, $meta);
        
    }
}