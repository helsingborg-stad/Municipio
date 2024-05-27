<?php

namespace Municipio\ExternalContent\WpPostMetaFactory;

use Spatie\SchemaOrg\BaseType;

class WpPostMetaFactory implements WpPostMetaFactoryInterface
{
    public function create(BaseType $schemaObject): array
    {
        return [
            'schemaData' => $schemaObject->toArray()
        ];
    }
}
