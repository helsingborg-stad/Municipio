<?php

namespace Municipio\ExternalContent\Sources\SourceDecorators;

use Municipio\ExternalContent\Sources\SourceInterface;
use Spatie\SchemaOrg\BaseType;
use WP_Query;

class DecorateSchemaObjectsWithLocalIds implements SourceInterface
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
        $object = $this->inner->getObject($id);

        if (null === $object) {
            return null;
        }

        return $this->setLocalIdOnObject($object);
    }

    public function getObjects(?WP_Query $query = null): array
    {
        $objects = $this->inner->getObjects($query);
        return array_map(fn(BaseType $object) => $this->setLocalIdOnObject($object), $objects);
    }

    private function setLocalIdOnObject(BaseType $object): BaseType
    {
        $object['@id'] = -(int)($this->getId() . $object['@id']);
        return $object;
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
