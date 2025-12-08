<?php

namespace Municipio\SchemaData\ExternalContent\JsonToSchemaObjects;

interface JsonToSchemaObjectsFactoryInterface
{
    public function create(): JsonToSchemaObjectsInterface;
}