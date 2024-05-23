<?php

namespace Municipio\ExternalContent\SchemaObjectToWpPost;

use Municipio\ExternalContent\SchemaObjectToWpPost\Helpers\GetSourceIdFromPostId;
use Municipio\ExternalContent\Sources\ISourceRegistry;
use Spatie\SchemaOrg\BaseType;
use WP_Post;

class ApplyPostType implements ApplySchemaObjectPropertiesToWpPost
{
    public function __construct(private ISourceRegistry $sourceRegistry, private GetSourceIdFromPostId $helpers, private ApplySchemaObjectPropertiesToWpPost $inner)
    {
    }

    public function apply(WP_Post $post, BaseType $schemaObject): WP_Post
    {
        $post     = $this->inner->apply($post, $schemaObject);
        $sourceId = $this->helpers->getSourceIdFromPostId($post->ID);

        if (!empty($sourceId)) {
            $source          = $this->sourceRegistry->getSourceById($sourceId);
            $post->post_type = $source->getPostType();
        }

        return $post;
    }
}
