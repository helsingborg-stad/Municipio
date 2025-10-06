<?php

namespace Municipio\SchemaData\ExternalContent\JsonToSchemaObjects;

interface JsonToSchemaObjectsFactoryInterface
{
    public static function create(): JsonToSchemaObjectsInterface;
}