<?php

namespace Municipio\ExternalContent\JsonToSchemaObjects;

interface JsonToSchemaObjects {

    /**
     * @param string $json
     * 
     * @return Municipio\Schema\BaseType[]
     */
    public function transform(string $json): array;
}