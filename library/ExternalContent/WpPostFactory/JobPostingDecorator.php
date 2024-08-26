<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\JobPosting;

/**
 * Decorate specifically for JobPosting schema type.
 */
class JobPostingDecorator implements WpPostFactoryInterface
{
    /**
     * Class constructor.
     *
     * @param WpPostFactoryInterface $inner The inner WpPostFactoryInterface instance.
     */
    public function __construct(private WpPostFactoryInterface $inner)
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
