<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\Config\Features\SchemaData\Contracts\TryGetSchemaTypeFromPostType;
use Municipio\PostObject\PostObjectInterface;
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
     * @param WP_Post|PostObjectInterface $post
     *
     * @return BaseType
     */
    public function create(WP_Post|PostObjectInterface $post): BaseType
    {
        $postType   = $post instanceof PostObjectInterface ? $post->getPostType() : $post->post_type;
        $schemaType = $this->config->tryGetSchemaTypeFromPostType($postType);

        if (!empty($schemaType) && method_exists(Schema::class, $schemaType)) {
            return Schema::$schemaType();
        }

        return Schema::Thing();
    }
}
