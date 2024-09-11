<?php

namespace Municipio\ExternalContent\Config;

interface ArrayFactoryInterface
{
    /**
     * Create an array.
     *
     * @return array The created array.
     */
    public function create(): array;
}
