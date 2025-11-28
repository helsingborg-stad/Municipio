<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\Schema\BaseType;

/**
 * Class DateDecorator
 */
class DateDecorator implements WpPostArgsFromSchemaObjectInterface
{
    /**
     * DateDecorator constructor.
     *
     * @param WpPostArgsFromSchemaObjectInterface $inner
     */
    public function __construct(private WpPostArgsFromSchemaObjectInterface $inner)
    {
    }

    /**
     * @inheritDoc
     */
    public function transform(BaseType $schemaObject): array
    {
        return array_merge(
            $this->inner->transform($schemaObject),
            [
                'post_date'     => $schemaObject['datePublished'] ?? null,
                'post_modified' => $schemaObject['dateModified'] ?? null,
            ]
        );
    }
}
