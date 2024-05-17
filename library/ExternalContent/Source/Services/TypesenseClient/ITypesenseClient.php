<?php

namespace Municipio\ExternalContent\Source\Services\TypesenseClient;

interface ITypesenseClient
{
    public function search(array $searchParams): array;
    public function getAll(): array;
    public function getSingleBySchemaId(string $id): array;
}
