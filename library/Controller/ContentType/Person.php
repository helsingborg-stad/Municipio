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
    public function __construct()
    {
        $this->key   = 'person';
        $this->label = __('Person', 'municipio');

        $this->schemaParams = $this->applySchemaParamsFilter();

        parent::__construct($this->key, $this->label);
    }

    public function init(): void
    {
        $this->addHooks();
    }

    public function addHooks(): void
    {
    }

    protected function setSchemaParams(): array
    {
        return [
            'jobTitle'   => [
                'schemaType' => 'Text',
                'label'      => __('Job title', 'municipio')
            ],
            'email'      => [
                'schemaType' => 'Text',
                'label'      => __('Email', 'municipio')
            ],
            'telephone'  => [
                'schemaType' => 'Text',
                'label'      => __('Telephone', 'municipio')
            ],
            'givenName'  => [
                'schemaType' => 'Text',
                'label'      => __('First name', 'municipio')
            ],
            'familyName' => [
                'schemaType' => 'Text',
                'label'      => __('Last name', 'municipio')
            ],
            'image'      => [
                'schemaType' => 'ImageObject',
                'label'      => __('Image', 'municipio')
            ],
        ];
    }
    public function getStructuredData(int $postId): array
    {

        $structuredData = [
            '@type' => 'Person',
            'name'  => get_the_title($postId),
        ];

        $meta = [
            'jobTitle',
            'email',
            'telephone'
        ];

        return ContentTypeHelper::getStructuredData($postId, $structuredData, $meta);
    }
}
