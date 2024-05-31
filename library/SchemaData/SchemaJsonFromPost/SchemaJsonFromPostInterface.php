<?php

namespace Municipio\SchemaData\SchemaJsonFromPost;

use WP_Post;

interface SchemaJsonFromPostInterface {
    public function create(WP_Post $postId):string;
}