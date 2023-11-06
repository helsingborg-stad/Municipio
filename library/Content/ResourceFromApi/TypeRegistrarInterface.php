<?php

namespace Municipio\Content\ResourceFromApi;

interface TypeRegistrarInterface
{
    public function register(): void;
    public function isRegistered(): bool;
    public function getName(): string;
    public function getArguments(): array;
}
