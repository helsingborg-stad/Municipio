<?php

namespace Municipio\Content\ResourceFromApi;

interface ResourceRegistryInterface
{
    /**
     * @return void
     */
    public function addHooks(): void;
    /**
     * @return ResourceInterface[]
     */
    public function getRegistry(): array;
    /**
     * @param string $type
     * @return ResourceInterface[]
     */
    public function getByType(string $type): array;
    /**
     * @param string $name
     * @return ResourceInterface[]
     */
    public function getByName(string $name): array;
}
