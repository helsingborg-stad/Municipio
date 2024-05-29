<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\BaseType;
use WP_Post;

interface WpPostFactoryInterface
{
    public function create(BaseType $schemaObject, ISource $source): WP_Post;
}
