<?php

namespace Municipio\Config\Features\ExternalContent\SourceConfig;

interface TypesenseSourceConfigInterface extends SourceConfigInterface
{
    public function getHost(): string;
    public function getApiKey(): string;
    public function getPort(): int;
    public function getProtocol(): string;
    public function getCollection(): string;
}
