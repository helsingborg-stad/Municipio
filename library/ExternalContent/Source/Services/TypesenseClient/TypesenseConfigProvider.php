<?php

namespace Municipio\ExternalContent\Source\Services\TypesenseClient;

class TypesenseConfigProvider implements TypesenseConfig
{
    public function getApiKey(): string
    {
        return 'XF006aj4uJQtIoTypJhcniXoG0gr6MT8';
    }
    public function getHost(): string
    {
        return 'n58fatmzu7yve4qbp-1.a1.typesense.net';
    }
    public function getPort(): string
    {
        return '443';
    }
    public function getProtocol(): string
    {
        return 'https';
    }
    public function getConnectionTimeoutSeconds(): int
    {
        return 2;
    }
}
