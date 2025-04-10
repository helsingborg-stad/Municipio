<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use WP_Post;

/**
 * Class SchemaObjectWithNameFromTitle
 *
 * @package Municipio\SchemaData\SchemaObjectFromPost
 */
class SchemaObjectWithNameFromTitle implements SchemaObjectFromPostInterface
{
    /**
     * SchemaObjectWithNameFromTitle constructor.
     *
     * @param SchemaObjectFromPostInterface $inner
     */
    public function __construct(private SchemaObjectFromPostInterface $inner)
    {
    }

    /**
     * @inheritDoc
     */
    public function create(WP_Post|PostObjectInterface $post): BaseType
    {
        $schema         = $this->inner->create($post);
        $schema['name'] = $post instanceof PostObjectInterface ? $post->getTitle() : $post->post_title;

        return $schema;
    }
}
