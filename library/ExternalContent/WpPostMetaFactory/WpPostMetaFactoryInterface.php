<?php

namespace Municipio\ExternalContent\WpPostMetaFactory;

use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\BaseType;

interface WpPostMetaFactoryInterface
{
    /**
     * Create an array of post meta data based on the given schema object and source.
     *
     * @param BaseType $schemaObject The schema object.
     * @param ISource $source The source object.
     * @return array The array of post meta data.
     */
    public function create(BaseType $schemaObject, ISource $source): array;
}
