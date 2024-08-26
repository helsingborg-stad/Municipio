<?php

namespace Municipio\ExternalContent\Sources\Services\TypesenseClient;

interface TypesenseClientInterface
{
    public function search(array $searchParams): array;
    public function getAll(): array;
    public function getSingleBySchemaId(string $id): array;
}
