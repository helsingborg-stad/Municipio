<?php

namespace Municipio\ExternalContent\Sources\Services\TypesenseClient;

class TypesenseClientWithCache implements ITypesenseClient
{
    private $cache = [];

    public function __construct(
        private ITypesenseClient $client
    ) {
    }

    public function search(array $searchParams): array
    {
        $cacheKey = md5(json_encode($searchParams));

        if ($cached = $this->cache[$cacheKey] ?? null) {
            return $cached;
        }

        $result                 = $this->client->search($searchParams);
        $this->cache[$cacheKey] = $result;
        return $result;
    }

    public function getAll(): array
    {
        if ($cached = $this->cache['all'] ?? null) {
            return $cached;
        }

        $all                = $this->client->getAll();
        $this->cache['all'] = $all;
        return $all;
    }

    public function getSingleBySchemaId(string $id): array
    {
        if ($cached = $this->cache[$id] ?? null) {
            return $cached;
        }

        $result           = $this->client->getSingleBySchemaId($id);
        $this->cache[$id] = $result;
        return $result;
    }
}
