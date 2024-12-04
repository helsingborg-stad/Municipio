<?php

namespace Municipio\Api\Posts;

interface RequestHandlerConfigInterface
{
    public function getIdentifier(): string;
    public function getViewPaths(): array;
}