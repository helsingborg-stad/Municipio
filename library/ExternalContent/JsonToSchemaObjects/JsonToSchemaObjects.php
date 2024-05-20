<?php

namespace Municipio\ExternalContent\JsonToSchemaObjects;

interface JsonToSchemaObjects {

    /**
     * @param string $json
     * 
     * @return (Thing|Event|JobPosting)[]
     */
    public function transform(string $json): array;
}