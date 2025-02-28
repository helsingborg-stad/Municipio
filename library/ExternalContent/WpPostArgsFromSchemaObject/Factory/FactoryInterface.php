<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject\Factory;

use Municipio\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;

interface FactoryInterface
{
    /**
     * Create a new instance of the WpPostArgsFromSchemaObjectInterface.
     *
     * @return WpPostArgsFromSchemaObjectInterface
     */
    public function create(): WpPostArgsFromSchemaObjectInterface;
}
