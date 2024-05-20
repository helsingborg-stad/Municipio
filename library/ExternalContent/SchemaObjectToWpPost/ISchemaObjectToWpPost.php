<?php

namespace Municipio\ExternalContent\SchemaObjectToWpPost;

use WP_Post;

interface ISchemaObjectToWpPost
{
    /**
     * Transforms a schema object into a WordPress post.
     *
     * @return WP_Post The transformed WordPress post.
     */
    public function toWpPost(): WP_Post;
}
