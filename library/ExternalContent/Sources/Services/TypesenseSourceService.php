<?php

namespace Municipio\ExternalContent\Sources\Services;

use Municipio\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjects;
use Municipio\ExternalContent\Sources\ISource;
use Municipio\ExternalContent\Sources\ISourceFilter;
use Municipio\ExternalContent\Sources\Services\TypesenseClient\ITypesenseClient;
use Spatie\SchemaOrg\Event;
use Spatie\SchemaOrg\JobPosting;
use Spatie\SchemaOrg\Thing;

class TypesenseSourceService implements ISource
{
    public function __construct(
        private ITypesenseClient $typesenseClient,
        private string $postType,
        private string $schemaType,
        private JsonToSchemaObjects $jsonToSchemaObjects,
        private ?ISource $inner = null
    ) {
        if ($this->inner === null) {
            $this->inner = new SourceService($this->postType, $this->schemaType);
        }
    }

    public function getId(): int
    {
        return $this->inner->getId();
    }

    public function getObject(string|int $id): null|Thing|Event|JobPosting
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
    public function getObjects(?ISourceFilter $filter = null): array
    {
        $result = $this->typesenseClient->getAll();
        return $this->jsonToSchemaObjects->transform(json_encode($result));
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
