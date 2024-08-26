<?php

namespace Municipio\ExternalContent\Sources\Services;

use Municipio\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjects;
use Municipio\ExternalContent\Sources\SourceInterface;
use Municipio\ExternalContent\Sources\Services\TypesenseClient\TypesenseClientInterface;
use Spatie\SchemaOrg\BaseType;
use WP_Query;

class TypesenseSourceServiceDecorator implements SourceInterface
{
    public function __construct(
        private TypesenseClientInterface $typesenseClient,
        private JsonToSchemaObjects $jsonToSchemaObjects,
        private SourceInterface $inner,
    ) {
    }

    public function getObject(string|int $id): null|BaseType
    {
        $result  = $this->typesenseClient->getSingleBySchemaId($id);
        $json    = json_encode($result);
        $objects = $this->jsonToSchemaObjects->transform($json);
        $index   = array_search($id, array_column($objects, '@id'));

        return $objects[$index] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getObjects(?WP_Query $query = null): array
    {
        $result = $this->typesenseClient->getAll();
        return $this->jsonToSchemaObjects->transform(json_encode($result));
    }

    public function getPostType(): string
    {
        return $this->inner->getPostType();
    }

    public function getId(): string
    {
        return $this->inner->getId();
    }

    public function getSchemaObjectType(): string
    {
        return $this->inner->getSchemaObjectType();
    }
}
