<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\Factory;

use Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;

interface FactoryInterface
{
    /**
     * Create a new instance of the WpPostArgsFromSchemaObjectInterface.
     *
     * @return WpPostArgsFromSchemaObjectInterface
     */
    public function create(): WpPostArgsFromSchemaObjectInterface;
}
