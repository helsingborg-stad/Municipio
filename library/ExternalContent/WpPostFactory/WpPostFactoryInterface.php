<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\BaseType;

interface WpPostFactoryInterface
{
    /**
     * Create a array from a schema object to be used to insert/update a WP_Post.
     */
    public function create(BaseType $schemaObject, ISource $source): array;
}
