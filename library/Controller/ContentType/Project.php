<?php

namespace Municipio\Controller\ContentType;

/**
 * Class Project
 * @package Municipio\Controller\ContentType
 */
class Project extends ContentTypeFactory implements ContentTypeComplexInterface
{
    public function __construct()
    {
        $this->key   = 'project';
        $this->label = __('Project', 'municipio');

        $this->schemaParams = $this->applySchemaParamsFilter();

        $this->addSecondaryContentType(new Place());

        parent::__construct($this->key, $this->label);
    }
    public function addHooks(): void
    {
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
     * Get the schema entity.
     *
     * @param \Spatie\SchemaOrg\Graph $graph The schema graph.
     * @return void
     */
    protected function getSchemaEntity(\Spatie\SchemaOrg\Graph $graph)
    {
        return $graph->project(); // Return the specific schema entity for Project
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
    protected function legacyGetStructuredData(int $postId, \Spatie\SchemaOrg\Graph $entity): array
    {
        $post           = \Municipio\Helper\WP::getPost($postId);
        $structuredData = [];

        if ('project' === $post->post_type) {
            $founder     = get_the_terms($postId, 'organisation');
            $brands      = get_the_terms($postId, 'participants');
            $departments = get_the_terms($postId, 'operation');
            $sponsors    = get_the_terms($postId, 'partner');
        } else {
            $founder     = (array) get_post_meta($postId, 'founder');
            $brands      = (array) get_post_meta($postId, 'brands');
            $departments = (array) get_post_meta($postId, 'departments');
            $sponsors    = (array) get_post_meta($postId, 'sponsors');
        }

        if (is_iterable($founder) && !is_wp_error($founder)) {
            foreach ($founder as $founder) {
                $structuredData['founder'][] = [
                    '@type' => 'Organization',
                    'name'  => $founder
                ];
            }
        }
        if (is_iterable($brands) && !is_wp_error($brands)) {
            foreach ($brands as $brand) {
                $structuredData['brand'][] = [
                    '@type' => 'Organization',
                    'name'  => $brand
                ];
            }
        }
        if (is_iterable($sponsors) && !is_wp_error($sponsors)) {
            foreach ($sponsors as $sponsor) {
                $structuredData['sponsor'][] = [
                    '@type' => 'Organization',
                    'name'  => $sponsor
                ];
            }
        }
        if (is_iterable($departments) && !is_wp_error($departments)) {
            foreach ($departments as $department) {
                $structuredData['department'][] = [
                    '@type' => 'Organization',
                    'name'  => $department
                ];
            }
        }
        return $structuredData;
    }
}
