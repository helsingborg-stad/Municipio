<?php

namespace Municipio\ExternalContent\JsonToSchemaObjects;

interface JsonToSchemaObjects {

    /**
     * @param string $json
     * 
     * @return Spatie\SchemaOrg\BaseType[]
     */
    public function transform(string $json): array;
}