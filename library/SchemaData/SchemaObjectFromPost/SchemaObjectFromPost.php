<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use WP_Post;

/**
 * Class SchemaObjectFromPost
 *
 * @package Municipio\SchemaData\SchemaObjectFromPost
 */
class SchemaObjectFromPost implements SchemaObjectFromPostInterface
{
    /**
     * SchemaObjectFromPost constructor.
     *
     * @param TryGetSchemaTypeFromPostType $config
     */
    public function __construct(private TryGetSchemaTypeFromPostType $config)
    {
    }

    /**
     * Create a schema object from a post.
     *
     * @param WP_Post $post
     *
     * @return BaseType
     */
    public function create(WP_Post $post): BaseType
    {
        $schemaType = $this->config->tryGetSchemaTypeFromPostType($post->post_type);

        if (!empty($schemaType) && method_exists(Schema::class, $schemaType)) {
            return Schema::$schemaType();
        }

        return Schema::Thing();
    }
}
