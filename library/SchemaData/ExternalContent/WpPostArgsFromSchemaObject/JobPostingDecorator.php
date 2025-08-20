<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\Schema\BaseType;
use Municipio\Schema\JobPosting;

/**
 * Decorate specifically for JobPosting schema type.
 */
class JobPostingDecorator implements WpPostArgsFromSchemaObjectInterface
{
    /**
     * Class constructor.
     *
     * @param WpPostArgsFromSchemaObjectInterface $inner The inner WpPostArgsFromSchemaObjectInterface instance.
     */
    public function __construct(private WpPostArgsFromSchemaObjectInterface $inner)
    {
    }

    /**
     * @inheritDoc
     */
    public function transform(BaseType $schemaObject): array
    {
        $post = $this->inner->transform($schemaObject);

        if ($schemaObject instanceof JobPosting) {
            $post = $this->applyPropertiesFromJobPosting($post, $schemaObject);
        }

        return $post;
    }

    /**
     * Apply properties from JobPosting schema object.
     *
     * @param array $post The post array.
     * @param JobPosting $schemaObject The JobPosting schema object.
     * @return array The post array with properties applied.
     */
    private function applyPropertiesFromJobPosting(array $post, JobPosting $schemaObject): array
    {
        if (!empty($schemaObject['title'])) {
            $post['post_title'] = html_entity_decode($schemaObject['title']);
        }

        if (!empty($schemaObject['datePosted'])) {
            $post['post_date'] = $schemaObject['datePosted'];
        }

        return $post;
    }
}
