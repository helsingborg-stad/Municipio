<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\JobPosting;

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
    public function create(BaseType $schemaObject, SourceInterface $source): array
    {
        $post = $this->inner->create($schemaObject, $source);

        if ($schemaObject instanceof JobPosting) {
            $post = $this->applyPropertiesFromJobPosting($post, $schemaObject);
        }

        return $post;
    }

    private function applyPropertiesFromJobPosting(array $post, JobPosting $schemaObject): array
    {
        if (!empty($schemaObject['title'])) {
            $post['post_title'] = $schemaObject['title'];
        }

        if (!empty($schemaObject['datePosted'])) {
            $post['post_date'] = $schemaObject['datePosted'];
        }

        return $post;
    }
}
