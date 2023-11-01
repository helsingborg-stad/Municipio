<?php

namespace Municipio\Content\ResourceFromApi;

interface TypeRegistrarInterface
{
    public function register(string $name, array $arguments): bool;
}
