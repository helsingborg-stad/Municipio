<?php

namespace Municipio\ExternalContent\JsonToSchemaObjects;

interface JsonToSchemaObjects {

    /**
     * @param string $json
     * 
     * @return (BaseType)[]
     */
    public function transform(string $json): array;
}