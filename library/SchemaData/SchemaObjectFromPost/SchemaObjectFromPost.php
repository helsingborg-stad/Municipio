<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\SchemaData\Utils\IGetSchemaTypeFromPostType;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Schema;
use WP_Post;

class SchemaObjectFromPost implements SchemaObjectFromPostInterface
{
    public function __construct(private IGetSchemaTypeFromPostType $util)
    {
    }

    public function create(WP_Post $post): BaseType
    {
        $schemaType = $this->util->getSchemaTypeFromPostType($post->post_type);

        if (method_exists(Schema::class, $schemaType)) {
            return Schema::$schemaType();
        }

        return Schema::Thing();
    }
}
