<?php

namespace Municipio\Content\ResourceFromApi;

interface TypeRegistrarInterface
{
    /**
     * Constructor for the TypeRegistrarInterface.
     *
     * @param ResourceInterface $resource The resource object.
     */
    public function __construct(ResourceInterface $resource);


    /**
     * Registers a type.
     *
     * @return bool Returns true if the registration was successful, false otherwise.
     */
    public function register(): bool;
}
