<?php

namespace Municipio\ExternalContent\Source\Services;

use Municipio\ExternalContent\JsonToSchemaObjects\JsonToSchemaObjects;
use Municipio\ExternalContent\Source\SchemaSourceFilter;
use Municipio\ExternalContent\Source\SchemaSourceReader;
use Municipio\ExternalContent\Source\Services\TypesenseClient\ITypesenseClient;
use Spatie\SchemaOrg\Event;
use Spatie\SchemaOrg\JobPosting;
use Spatie\SchemaOrg\Thing;
use Typesense\Client;

class TypesenseSourceService implements SchemaSourceReader
{
    public function __construct(
        private ITypesenseClient $iTypesenseClient,
        private JsonToSchemaObjects $jsonToSchemaObjects
    ) {
    }

    public function getObject(string|int $id): null|Thing|Event|JobPosting
    {
        $result  = $this->iTypesenseClient->getSingleBySchemaId($id);
        $json    = json_encode($result);
        $objects = $this->jsonToSchemaObjects->transform($json);
        $index   = array_search($id, array_column($objects, '@id'));

        return $objects[$index] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getObjects(?SchemaSourceFilter $filter = null): array
    {
        $result = $this->iTypesenseClient->getAll();
        return $this->jsonToSchemaObjects->transform(json_encode($result));
    }
}
