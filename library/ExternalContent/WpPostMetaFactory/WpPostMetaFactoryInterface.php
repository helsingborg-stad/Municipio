<?php

namespace Municipio\ExternalContent\WpPostMetaFactory;

use Spatie\SchemaOrg\BaseType;

interface WpPostMetaFactoryInterface
{
    public function create(BaseType $schemaObject): array;
}
