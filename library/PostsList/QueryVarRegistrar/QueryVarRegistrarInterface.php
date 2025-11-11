<?php

namespace Municipio\PostsList\QueryVarRegistrar;

interface QueryVarRegistrarInterface
{
    public function register(string $queryVar): void;
}
