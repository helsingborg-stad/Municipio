<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Spatie\SchemaOrg\BaseType;
use WP_Post;

interface WpPostFactoryInterface
{
    public function create(BaseType $schemaObject): WP_Post;
}
