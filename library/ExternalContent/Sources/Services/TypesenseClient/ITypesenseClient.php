<?php

namespace Municipio\ExternalContent\Sources\Services\TypesenseClient;

interface ITypesenseClient
{
    public function search(array $searchParams): array;
    public function getAll(): array;
    public function getSingleBySchemaId(string $id): array;
}
