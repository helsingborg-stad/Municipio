<?php

namespace Municipio\ExternalContent\Sources\Services\TypesenseClient;

use Typesense\Client;

class TypesenseClient implements ITypesenseClient
{
    private ?Client $client = null;

    public function __construct(
        private string $apiKey,
        private string $host,
        private string $collectionName,
        private string $port = '443',
        private string $protocol = 'https',
        private int $connectionTimeoutSeconds = 2
    ) {
    }

    protected function trySetupClient(): void
    {
        if ($this->client !== null) {
            return;
        }

        $this->client = new Client([
            'api_key'                    => $this->apiKey,
            'connection_timeout_seconds' => $this->connectionTimeoutSeconds,
            'nodes'                      => [
                [
                    'host'     => $this->host,
                    'port'     => $this->port,
                    'protocol' => $this->protocol,
                ],
            ],
        ]);
    }

    public function search(array $searchParams): array
    {
        $this->trySetupClient();
        return $this->client->collections[$this->collectionName]->documents->search($searchParams);
    }

    public function getAll(): array
    {
        return $this->search([
            'q'        => '*',
            'query_by' => '@id',
        ]);
    }

    public function getSingleBySchemaId(string $id): array
    {
        return $this->search([
            'q'          => $id,
            'query_by'   => '@id',
            'filter_by'  => "@id:={$id}",
            'limit_hits' => 1,
            'per_page'   => 1,
        ]);
    }
}
