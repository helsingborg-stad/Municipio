<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\BaseType;
use WP_Post;

/**
 * Decorate specifically for JobPosting schema type.
 */
class WpPostFactoryJobPostingDecorator implements WpPostFactoryInterface
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
    public function create(BaseType $schemaObject, ISource $source): WP_Post
    {
        $post = $this->inner->create($schemaObject, $source);

        if ($schemaObject->getType() === 'JobPosting' && !empty($schemaObject['title'])) {
            $post->post_title = $schemaObject['title'];
        }

        return $post;
    }
}
