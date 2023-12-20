<?php

namespace Municipio\Content\ResourceFromApi;

interface ResourceInterface extends ResourceRequestInterface
{
    /**
     * Returns the name of the resource.
     */
    public function getName(): string;

    /**
     * Returns the type of the resource.
     */
    public function getType(): string;

    /**
     * Returns the arguments for registering the resource.
     */
    public function getBaseUrl(): string;

    /**
     * Returns the original name of the resource.
     */
    public function getOriginalName(): string;

    /**
     * Returns the base name of the resource.
     */
    public function getBaseName(): string;

    /**
     * Returns the media resource of the resource.
     */
    public function getMediaResource(): ?ResourceInterface;

    /**
     * Returns the post type of the resource.
     */
    public function getResourceID(): int;

    /**
     * Returns the arguments for registering the resource.
     */
    public function getArguments(): array;
}
