<?php

namespace Municipio\ExternalContent\Sources\Services;

use Municipio\ExternalContent\Sources\SourceInterface;
use Municipio\ExternalContent\Sources\ISourceFilter;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Thing;
use Spatie\SchemaOrg\Event;
use Spatie\SchemaOrg\JobPosting;
use WP_Query;

class DecorateSchemaObjectsWithLocalIds implements SourceInterface
{
    public function __construct(private SourceInterface $inner)
    {
    }

    public function getId(): int
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

    public function getType(): string
    {
        return $this->inner->getType();
    }
}
