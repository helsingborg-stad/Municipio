<?php

namespace Municipio\ExternalContent\Source\Services\TypesenseClient;

interface TypesenseConfig
{
    public function getApiKey(): string;
    public function getHost(): string;
    public function getPort(): string;
    public function getProtocol(): string;
    public function getConnectionTimeoutSeconds(): int;
}
