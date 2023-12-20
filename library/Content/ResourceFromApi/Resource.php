<?php

namespace Municipio\Content\ResourceFromApi;

/**
 * Class Resource
 * Represents a resource from an API.
*/
abstract class Resource implements ResourceInterface
{
    private int $id;
    private string $name;
    private array $arguments;
    private string $baseUrl;
    private string $originalName;
    private string $baseName;
    private ?ResourceInterface $mediaResource;


    /**
     * Class Resource
     * Represents a resource obtained from an API.
     *
     * @param int $id The ID of the resource.
     * @param string $name The name of the resource.
     * @param array $arguments An array of arguments for registering the resource.
     * @param string $baseUrl The base URL of the resource.
     * @param string $originalName The original name of the resource.
     * @param string $baseName The base name of the resource.
     * @param ResourceInterface|null $mediaResource The media resource of the resource.
     */
    public function __construct(
        int $id,
        string $name,
        array $arguments,
        string $baseUrl,
        string $originalName,
        string $baseName,
        ?ResourceInterface $mediaResource = null
    ) {
        $this->id            = $id;
        $this->name          = $name;
        $this->arguments     = $arguments;
        $this->baseUrl       = $baseUrl;
        $this->originalName  = $originalName;
        $this->baseName      = $baseName;
        $this->mediaResource = $mediaResource;
    }

    /**
     * Returns the ID of the resource.
     *
     * @return int The ID of the resource.
     */
    public function getResourceID(): int
    {
        return $this->id;
    }

    /**
     * Get the name of the resource.
     *
     * @return string The name of the resource.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the arguments for the resource.
     *
     * @return array The arguments for the resource.
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Get the base URL of the resource.
     *
     * @return string The base URL of the resource.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get the original name of the resource.
     *
     * @return string The original name of the resource.
     */
    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    /**
     * Get the base name of the resource.
     *
     * @return string The base name of the resource.
     */
    public function getBaseName(): string
    {
        return $this->baseName;
    }

    /**
     * Get the media resource of the resource.
     *
     * @return ResourceInterface|null The media resource of the resource.
     */
    public function getMediaResource(): ?ResourceInterface
    {
        return $this->mediaResource;
    }

    /**
     * Get the post type of the resource.
     *
     * @return string The post type of the resource.
     */
    abstract public function getType(): string;

    /**
     * Get the post type arguments of the resource.
     *
     * @return array The post type arguments of the resource.
     */
    abstract public function getCollection(?array $queryArgs = null): array;

    /**
     * Get the post type arguments of the resource.
     *
     * @return array The post type arguments of the resource.
     */
    abstract public function getCollectionHeaders(?array $queryArgs = null): array;

    /**
     * Get the post type arguments of the resource.
     *
     * @return array The post type arguments of the resource.
     */
    abstract public function getSingle($id): ?object;

    /**
     * Get the post type arguments of the resource.
     *
     * @return array The post type arguments of the resource.
     */
    abstract public function getMeta(int $id, string $metaKey, bool $single = true);
}
