<?php

namespace Municipio\Content\ResourceFromApi;

abstract class Resource implements ResourceInterface
{
    private int $id;
    private string $name;
    private array $arguments;
    private string $baseUrl;
    private string $originalName;
    private string $baseName;
    private ?ResourceInterface $mediaResource;

    public function __construct(int $id, string $name, array $arguments, string $baseUrl, string $originalName, string $baseName, ?ResourceInterface $mediaResource = null)
    {
        $this->id = $id;
        $this->name = $name;
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

    abstract public function getType(): string;

    abstract public function getCollection(?array $queryArgs = null): array;

    abstract public function getCollectionHeaders(?array $queryArgs = null): array;

    abstract public function getSingle($id): ?object;

    abstract public function getMeta(int $id, string $metaKey, bool $single = true);
}
