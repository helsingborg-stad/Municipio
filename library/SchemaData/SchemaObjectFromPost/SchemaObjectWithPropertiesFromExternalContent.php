<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use WP_Post;
use WpService\Contracts\GetPostMeta;

/**
 * Class SchemaObjectWithPropertiesFromExternalContent
 *
 * @package Municipio\SchemaData\SchemaObjectFromPost
 */
class SchemaObjectWithPropertiesFromExternalContent implements SchemaObjectFromPostInterface
{
    /**
     * SchemaObjectWithPropertiesFromExternalContent constructor.
     *
     * @param GetPostMeta $wpService
     * @param SchemaObjectFromPostInterface $inner
     */
    public function __construct(
        private GetPostMeta $wpService,
        private SchemaObjectFromPostInterface $inner,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function create(WP_Post|PostObjectInterface $post): BaseType
    {
        $id         = $post instanceof PostObjectInterface ? $post->getId() : $post->ID;
        $schemaData = $this->wpService->getPostMeta($id, 'schemaData', true);

        if (
            !empty($schemaData) &&
            isset($schemaData['@type'])
        ) {
            $schema = call_user_func(array(new Schema(), $schemaData['@type']));

            foreach ($schemaData as $propertyName => $propertyValue) {
                $schema->setProperty($propertyName, $propertyValue);
            }

            return $schema;
        }

        return $this->inner->create($post);
    }
}
