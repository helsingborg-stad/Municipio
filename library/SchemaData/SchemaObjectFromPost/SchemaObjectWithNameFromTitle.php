<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Spatie\SchemaOrg\BaseType;
use WP_Post;

class SchemaObjectWithNameFromTitle implements SchemaObjectFromPostInterface
{
    public function __construct(private SchemaObjectFromPostInterface $inner)
    {
    }

    public function create(WP_Post $post): BaseType
    {
        $schema         = $this->inner->create($post);
        $schema['name'] = $post->post_title;

        return $schema;
    }
}
