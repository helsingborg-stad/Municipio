<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

interface FactoryInterface
{
    /**
     * Create a SchemaObjectFromPostInterface instance.
     *
     * @return SchemaObjectFromPostInterface
     */
    public function create(): SchemaObjectFromPostInterface;
}
