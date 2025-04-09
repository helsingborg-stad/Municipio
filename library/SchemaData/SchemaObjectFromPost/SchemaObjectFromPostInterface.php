<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use WP_Post;

interface SchemaObjectFromPostInterface
{
    /**
     * Create a schema object from a post.
     *
     * @param WP_Post|PostObjectInterface $post
     *
     * @return BaseType
     */
    public function create(WP_Post|PostObjectInterface $post): BaseType;
}
