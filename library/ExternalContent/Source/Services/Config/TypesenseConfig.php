<?php

namespace Municipio\ExternalContent\Source\Services\Config;

interface TypesenseConfig
{
    public function getApiKey(): string;
    public function getHost(): string;
    public function getPort(): string;
    public function getProtocol(): string;
    public function getConnectionTimeoutSeconds(): int;
}
