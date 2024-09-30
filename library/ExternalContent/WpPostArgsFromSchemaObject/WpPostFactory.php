<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;

/**
 * Class WpPostFactory
 *
 * This class is responsible for creating WordPress post arguments from a schema object.
 */
class WpPostFactory implements WpPostArgsFromSchemaObjectInterface
{
    /**
     * Creates WordPress post arguments from a schema object.
     *
     * @param BaseType $schemaObject The schema object containing post data.
     * @param SourceInterface $source The source interface providing additional data.
     * @return array The array of WordPress post arguments.
     */
    public function create(BaseType $schemaObject, SourceInterface $source): array
    {
        $title   = html_entity_decode($schemaObject['name'] ?? '');
        $title   = urldecode($title);
        $content = html_entity_decode($schemaObject['description'] ?? '');
        $content = urldecode($content);

        return [
            'post_title'   => $title,
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_type'    => $source->getPostType(),
            'meta_input'   => []
        ];
    }
}
