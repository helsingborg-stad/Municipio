<?php

namespace Municipio\ExternalContent\WpPostFactory;

use Municipio\ExternalContent\Sources\ISource;
use Spatie\SchemaOrg\BaseType;

class WpPostFactory implements WpPostFactoryInterface
{
    public function create(BaseType $schemaObject, ISource $source): array
    {
        return [
            'post_title'   => $schemaObject['name'] ?? '',
            'post_content' => $schemaObject['description'] ?? '',
            'post_status'  => 'publish',
            'post_type'    => $source->getPostType(),
            'meta_input'   => []
        ];
    }
}
