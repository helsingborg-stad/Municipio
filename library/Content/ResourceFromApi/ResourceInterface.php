<?php

namespace Municipio\Content\ResourceFromApi;

interface ResourceInterface
{
    public function getName(): string;
    public function getType(): string;
    public function getBaseUrl(): string;
    public function getOriginalName(): string;
    public function getBaseName(): string;
    public function getMediaResource(): ?ResourceInterface;
    public function getResourceID(): int;
    public function getArguments(): array;
}
