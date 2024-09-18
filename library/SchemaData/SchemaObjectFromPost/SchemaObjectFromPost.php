<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Schema;
use WP_Post;

class SchemaObjectFromPost implements SchemaObjectFromPostInterface
{
    public function __construct(private TryGetSchemaTypeFromPostType $config)
    {
    }

    public function create(WP_Post $post): BaseType
    {
        $schemaType = $this->config->tryGetSchemaTypeFromPostType($post->post_type);

        if (!empty($schemaType) && method_exists(Schema::class, $schemaType)) {
            return Schema::$schemaType();
        }

        return Schema::Thing();
    }
}
