<?php

namespace Municipio\ExternalContent\Sources\SourceDecorators;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;
use WP_Query;

class FilterOutDuplicateObjectsFromSource implements SourceInterface
{
    public function __construct(private SourceInterface $inner)
    {
    }

    public function getId(): string
    {
        return $this->inner->getId();
    }

    public function getObject(string|int $id): null|BaseType
    {
        return $this->inner->getObject($id);
    }

    public function getObjects(?WP_Query $query = null): array
    {
        $objects   = $this->inner->getObjects($query);
        $schemaIds = [];

        foreach ($objects as $index => $object) {
            if (!empty($object->getProperty('@id')) && in_array($object->getProperty('@id'), $schemaIds)) {
                unset($objects[$index]);
            } else {
                $schemaIds[] = $object->getProperty('@id');
            }
        }

        return $objects;
    }

    public function getPostType(): string
    {
        return $this->inner->getPostType();
    }

    public function getSchemaObjectType(): string
    {
        return $this->inner->getSchemaObjectType();
    }
}
