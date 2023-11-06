<?php

namespace Municipio\Content\ResourceFromApi;

interface QueriesModifierInterface
{
    public function __construct(ResourceRegistryInterface $resourceRegistry);
    public function addHooks(): void;
}