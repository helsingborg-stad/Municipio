<?php

namespace Municipio\Content\ResourceFromApi;

class Resource implements ResourceInterface
{
    private int $id;
    private string $name;
    private string $type;
    private array $arguments;
    private string $baseUrl;
    private string $originalName;
    private string $baseName;
    private ?ResourceInterface $mediaResource;

    public function __construct(int $id, string $name, string $type, array $arguments, string $baseUrl, string $originalName, string $baseName, ?ResourceInterface $mediaResource = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->arguments = $arguments;
        $this->baseUrl = $baseUrl;
        $this->originalName = $originalName;
        $this->baseName = $baseName;
        $this->mediaResource = $mediaResource;
    }

    public function getResourceID(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function getBaseName(): string
    {
        return $this->baseName;
    }

    public function getMediaResource(): ?ResourceInterface
    {
        return $this->mediaResource;
    }
}
