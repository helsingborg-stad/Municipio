<?php

namespace Municipio\ExternalContent\JsonToSchemaObjects;

interface JsonToSchemaObjects {
    public function transform(string $json): array;
}