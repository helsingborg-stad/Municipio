<?php

namespace Municipio\Controller\ContentType\Complex;

use Municipio\Controller\ContentType;

/**
 * Class Project
 * @package Municipio\Controller\ContentType
 */
class Project extends ContentType\ContentTypeFactory implements ContentType\ContentTypeComplexInterface
{
    use ContentType\Traits\AddSecondaryContentType;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->key   = 'project';
        $this->label = __('Project', 'municipio');

        $this->addSecondaryContentType(new ContentType\Simple\Place());
        $this->schemaParams = $this->applySchemaParamsFilter();

        parent::__construct($this->key, $this->label);
    }


    /**
     * Set the schema parameters.
     *
     * @return array
     */
    protected function schemaParams(): array
    {
        $params = [
        'name'        => [
            'schemaType' => 'Text',
            'label'      => _x('Name', 'Project name', 'municipio')
        ],
        'description' => [
            'schemaType' => 'Text',
            'label'      => __('Description', 'municipio')
        ],
        'image'       => [
            'schemaType' => 'ImageObject',
            'label'      => __('Image', 'municipio')
        ],
        'url'         => [
            'schemaType' => 'URL',
            'label'      => __('URL', 'municipio')
        ],
        'founder'     => [
            'schemaType' => 'Organisation',
            'label'      => _x('Founder', 'Project founder, commonly "organisation".', 'municipio')
        ],
        'brand'       => [
            'schemaType' => 'Organisation',
            'label'      => _x('Brand', 'Project brand, commonly "participants"', 'municipio')
        ],
        'department'  => [
            'schemaType' => 'Organisation',
            'label'      => _x('Department', 'Project department', 'municipio')
        ],
        ];

        foreach ($this->getSecondaryContentType() as $contentType) {
            switch ($contentType->getKey()) {
                case 'place':
                    $placeParams       = $contentType->getSchemaParams();
                    $params['address'] = $placeParams['geo'];
                    break;

                default:
                    break;
            }
        }

        return $params;
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
    protected function legacyGetStructuredData(int $postId, $entity): ?array
    {
        if (empty($entity)) {
            return [];
        }

        $entity->name(get_the_title($postId));
        $entity->description(get_the_excerpt($postId));
        $entity->url(get_permalink($postId));

        $founder     = get_the_terms($postId, 'organisation');
        $brands      = get_the_terms($postId, 'participants');
        $departments = get_the_terms($postId, 'operation');
        $sponsors    = get_the_terms($postId, 'partner');


        if (is_iterable($founder) && !is_wp_error($founder)) {
            foreach ($founder as $founder) {
                $entity->founder($founder);
            }
        }
        if (is_iterable($brands) && !is_wp_error($brands)) {
            foreach ($brands as $brand) {
                $entity->brand($brand);
            }
        }
        if (is_iterable($sponsors) && !is_wp_error($sponsors)) {
            foreach ($sponsors as $sponsor) {
                $entity->sponsor($sponsor);
            }
        }
        if (is_iterable($departments) && !is_wp_error($departments)) {
            foreach ($departments as $department) {
                $entity->department($department);
            }
        }

        return $entity->toArray();
    }
}
