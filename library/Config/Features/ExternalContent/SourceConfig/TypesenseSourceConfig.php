<?php

namespace Municipio\Config\Features\ExternalContent\SourceConfig;

class TypesenseSourceConfig implements TypesenseSourceConfigInterface
{
    public function __construct(
        private string $apiKey,
        private string $protocol,
        private string $host,
        private int $port,
        private string $collection,
    ) {
    }

    public function getType(): string
    {
        return 'typesense';
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getCollection(): string
    {
        return $this->collection;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getProtocol(): string
    {
        return $this->protocol;
    }
}
