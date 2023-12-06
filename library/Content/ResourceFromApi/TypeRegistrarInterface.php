<?php

namespace Municipio\Content\ResourceFromApi;

interface TypeRegistrarInterface
{
    public function __construct(ResourceInterface $resource);
    public function register(): bool;
}
