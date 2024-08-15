<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\SchemaData\Utils\GetSchemaTypeFromPostTypeInterface;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Schema;
use WP_Post;

class SchemaObjectFromPost implements SchemaObjectFromPostInterface
{
    public function __construct(private GetSchemaTypeFromPostTypeInterface $util)
    {
    }

    public function create(WP_Post $post): BaseType
    {
        $schemaType = $this->util->getSchemaTypeFromPostType($post->post_type);

        if (!empty($schemaType) && method_exists(Schema::class, $schemaType)) {
            return Schema::$schemaType();
        }

        return Schema::Thing();
    }
}
