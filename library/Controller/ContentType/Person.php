<?php

namespace Municipio\Controller\ContentType;

/**
 * Class Person
 *
 * @package Municipio\Controller\ContentType
 */
class Person extends ContentTypeFactory
{
    /**
     * Constructor method for the Person content type.
     */
    public function __construct()
    {
        $this->key   = 'person';
        $this->label = __('Person', 'municipio');

        $this->schemaParams = $this->applySchemaParamsFilter();

        parent::__construct($this->key, $this->label);
    }

    /**
     * Get the schema parameters.
     *
     * @return array The schema parameters.
     */
    protected function schemaParams(): array
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
    /**
     * @param int $postId The ID of the post.
     * @return array The structured data.
     */
    public function legacyGetStructuredData(int $postId, $entity): ?array
    {
        return [];
    }
}
