<?php

namespace Municipio\ExternalContent\SchemaObjectToWpPost;

use Spatie\SchemaOrg\BaseType;
use WP_Post;

interface ApplySchemaObjectPropertiesToWpPost
{
    public function apply(WP_Post $post, BaseType $schemaObject): WP_Post;
}
