<?php

namespace Municipio\ExternalContent\WpPostMetaFactory;

use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\BaseType;

interface WpPostMetaFactoryInterface
{
    public function create(BaseType $schemaObject, ISource $source): array;
}
