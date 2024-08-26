<?php

namespace Municipio\ExternalContent\Sources\Services\TypesenseClient;

class TypesenseClientWithCache implements TypesenseClientInterface
{
    private static $cache = [];

    public function __construct(
        private TypesenseClientInterface $client
    ) {
    }

    public function search(array $searchParams): array
    {
        $cacheKey = md5(json_encode($searchParams));

        if ($cached = self::$cache[$cacheKey] ?? null) {
            return $cached;
        }

        return self::$cache[$cacheKey] = $this->client->search($searchParams);
    }

    public function getAll(): array
    {
        if ($cached = self::$cache['all'] ?? null) {
            return $cached;
        }

        return self::$cache['all'] = $this->client->getAll();
    }

    public function getSingleBySchemaId(string $id): array
    {
        if ($cached = self::$cache[$id] ?? null) {
            return $cached;
        }

        return self::$cache[$id] = $this->client->getSingleBySchemaId($id);
    }
}
