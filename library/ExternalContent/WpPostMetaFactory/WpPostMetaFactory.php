<?php

namespace Municipio\ExternalContent\WpPostMetaFactory;

use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\BaseType;

class WpPostMetaFactory implements WpPostMetaFactoryInterface
{
    public function create(BaseType $schemaObject, ISource $source): array
    {
        return [
            'schemaData' => $schemaObject->toArray()
        ];
    }
}
