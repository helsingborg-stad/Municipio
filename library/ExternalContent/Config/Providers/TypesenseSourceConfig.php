<?php

namespace Municipio\ExternalContent\Config\Providers;

use Municipio\ExternalContent\Config\TypesenseSourceConfigInterface;

class TypesenseSourceConfig extends SourceConfig implements TypesenseSourceConfigInterface
{
    public function __construct(
        protected string $postType,
        protected string $schemaObjectType,
        protected string $apiKey,
        protected string $host,
        protected string $collectionName,
        protected string $port = '443',
        protected string $protocol = 'https',
        protected int $connectionTimeoutSeconds = 5
    ) {
        parent::__construct($postType, $schemaObjectType);
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getCollectionName(): string
    {
        return $this->collectionName;
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
