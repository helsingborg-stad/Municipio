<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\Schema\BaseType;
use WP_Post;

/**
 * Class SchemaObjectWithNameFromTitle
 *
 * @package Municipio\SchemaData\SchemaObjectFromPost
 */
class SchemaObjectWithNameFromTitle implements SchemaObjectFromPostInterface
{
    /**
     * SchemaObjectWithNameFromTitle constructor.
     *
     * @param SchemaObjectFromPostInterface $inner
     */
    public function __construct(private SchemaObjectFromPostInterface $inner)
    {
    }

    /**
     * Create a schema object from a post.
     *
     * @param WP_Post $post
     *
     * @return BaseType
     */
    public function create(WP_Post $post): BaseType
    {
        $schema         = $this->inner->create($post);
        $schema['name'] = $post->post_title;

        return $schema;
    }
}
