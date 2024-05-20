<?php

namespace Municipio\ExternalContent\Source\Services\TypesenseClient;

class TypesenseConfigProvider implements TypesenseConfig
{
    public function __construct(
        private string $apiKey,
        private string $host,
        private string $port = '443',
        private string $protocol = 'https',
        private int $connectionTimeoutSeconds = 2
    ) {
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }
    public function getHost(): string
    {
        return $this->host;
    }
    public function getPort(): string
    {
        return $this->port;
    }
    public function getProtocol(): string
    {
        return $this->protocol;
    }
    public function getConnectionTimeoutSeconds(): int
    {
        return $this->connectionTimeoutSeconds;
    }
}
