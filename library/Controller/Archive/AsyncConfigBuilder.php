<?php

namespace Municipio\Controller\Archive;

/**
 * Decorator for async-specific attributes for archive posts list
 */
class AsyncConfigBuilder
{
    private array $baseConfig = [];
    private array $asyncAttributes = [];

    public function withBaseConfig(array $config): self
    {
        $this->baseConfig = $config;
        return $this;
    }

    public function setAsyncId($id): self
    {
        $this->asyncAttributes['asyncId'] = $id;
        return $this;
    }

    public function setIsAsync(bool $isAsync = true): self
    {
        $this->asyncAttributes['isAsync'] = $isAsync;
        return $this;
    }

    public function build(): array
    {
        return array_merge($this->baseConfig, $this->asyncAttributes);
    }
}
