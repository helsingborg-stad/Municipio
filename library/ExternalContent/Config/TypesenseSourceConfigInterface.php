<?php

namespace Municipio\ExternalContent\Config;

interface TypesenseSourceConfigInterface extends SourceConfigInterface
{
    public function getApiKey(): string;
    public function getHost(): string;
    public function getCollectionName(): string;
    public function getPort(): string;
    public function getProtocol(): string;
    public function getConnectionTimeoutSeconds(): int;
}
