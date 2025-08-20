<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;
use Municipio\Schema\BaseType;

/**
 * Class EventDatesDecorator
 */
class EventDatesDecorator implements WpPostArgsFromSchemaObjectInterface
{
    /**
     * EventDatesDecorator constructor.
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
        if ($schemaObject->getType() !== 'Event') {
            return $this->inner->transform($schemaObject);
        }

        $post      = $this->inner->transform($schemaObject);
        $startDate = $schemaObject->getProperty('startDate');
        $endDate   = $schemaObject->getProperty('endDate');

        $post['meta_input']['startDate'] = $startDate;
        $post['meta_input']['endDate']   = $endDate;

        return $post;
    }
}
