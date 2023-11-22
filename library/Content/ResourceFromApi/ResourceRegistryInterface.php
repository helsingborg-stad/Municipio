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
    public static function getRegistry(): array;
    /**
     * @param string $type
     * @return ResourceInterface[]
     */
    public static function getByType(string $type): array;
    /**
     * @param string $name
     * @return ResourceInterface[]
     */
    public static function getByName(string $name): array;
}
