<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\Schema\BaseType;
use WP_Post;

interface SchemaObjectFromPostInterface
{
    public function create(WP_Post $post): BaseType;
}
