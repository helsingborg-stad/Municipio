<?php

namespace Municipio\ExternalContent\Source\Services\TypesenseClient;

use Municipio\ExternalContent\Source\Services\Config\TypesenseConfig;
use Typesense\Client;

class TypesenseClient implements ITypesenseClient
{
    private ?Client $client = null;

    public function __construct(private TypesenseConfig $config, private string $collectionName)
    {
    }

    protected function trySetupClient(): void
    {
        if ($this->client !== null) {
            return;
        }

        $this->client = new Client([
            'api_key'                    => $this->config->getApiKey(),
            'connection_timeout_seconds' => $this->config->getConnectionTimeoutSeconds(),
            'nodes'                      => [
                [
                    'host'     => $this->config->getHost(),
                    'port'     => $this->config->getPort(),
                    'protocol' => $this->config->getProtocol(),
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
