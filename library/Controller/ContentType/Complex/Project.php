<?php

namespace Municipio\Controller\ContentType\Complex;

use Municipio\Controller\ContentType;

/**
 * Class Project
 * @package Municipio\Controller\ContentType
 */
class Project extends ContentType\ContentTypeFactory 
{

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
            if ($contentType->getKey() === 'place') {
                $placeParams       = $contentType->getSchemaParams();
                $params['address'] = $placeParams['geo'];
            }
        }

        return $params;
    }

    /**
     * Appends the structured data array (used for schema.org markup) with additional data.
     *
     * @param int $postId The ID of the post you want to add structured data to.
     * @param object $entity The schema entity object to append data to.
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

        $this->processEntityTerms($entity, $postId, 'organisation', 'founder');
        $this->processEntityTerms($entity, $postId, 'participants', 'brand');
        $this->processEntityTerms($entity, $postId, 'partner', 'sponsor');
        $this->processEntityTerms($entity, $postId, 'operation', 'department');

        return $entity->toArray();
    }

    /**
     * Processes and appends terms to the entity object for a given taxonomy.
     *
     * @param object $entity The entity to append terms to.
     * @param int $postId The ID of the post to get terms for.
     * @param string $taxonomy The taxonomy to retrieve terms from.
     * @param string $method The method of the entity to call for each term.
     */
    protected function processEntityTerms($entity, int $postId, string $taxonomy, string $method)
    {
        $terms = get_the_terms($postId, $taxonomy);

        if (is_iterable($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $entity->$method($term);
            }
        }
    }
}
